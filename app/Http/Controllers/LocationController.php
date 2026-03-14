<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:locations-create')->only(['create', 'store', 'importTemplate', 'import']);
        $this->middleware('can:locations-read')->only(['index', 'show', 'exportCsv']);
        $this->middleware('can:locations-update')->only(['edit', 'update']);
        $this->middleware('can:locations-delete')->only('destroy');
    }

    // -------------------------------------------------------------------------
    // CRUD
    // -------------------------------------------------------------------------

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Location::with('updatedBy')->select('locations.*'))
                ->addIndexColumn()
                ->editColumn('code', function ($model) {
                    return '<a href="' . route('admin.locations.show', $model->id) . '">' . $model->code . '</a>';
                })
                ->addColumn('is_active', function ($model) {
                    return $model->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('last_updated_at', function ($model) {
                    return $model->last_updated_at
                        ? $model->last_updated_at->format('Y-m-d H:i:s')
                        : '-';
                })
                ->addColumn('last_updated_by', function ($model) {
                    return $model->updatedBy?->name ?? '-';
                })
                ->addColumn('options', 'admin.locations.datatables.options')
                ->setRowAttr([
                    'data-model-id' => function ($model) {
                        return $model->id;
                    }
                ])
                ->rawColumns(['code', 'is_active', 'options'])
                ->toJson();
        }

        return view('admin.locations.index');
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        $location = new Location($request->validated());
        $location->stampUpdatedBy(auth()->id());
        $location->save();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show($id)
    {
        $data = Location::with('updatedBy')->findOrFail($id);
        return view('admin.locations.show', compact('data'));
    }

    public function edit($id)
    {
        $data = Location::with('updatedBy')->findOrFail($id);
        return view('admin.locations.edit', compact('data'));
    }

    public function update(UpdateLocationRequest $request, $id)
    {
        $location = Location::findOrFail($id);
        $location->fill($request->validated());
        $location->stampUpdatedBy(auth()->id());
        $location->save();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Import Template  GET /admin/locations/import-template
    // -------------------------------------------------------------------------

    public function importTemplate()
    {
        $filename = 'locations_import_template.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'code',
                'company_name',
                'name',
                'address',
                'city',
                'coordinate',
                'is_active',
            ]);

            // Example rows
            fputcsv($handle, [
                'wsid_b1120874',
                'PT Wisata Indah',
                'Wisata Indah Building',
                'Jl. Sudirman No. 1, Jakarta',
                'Jakarta',
                '-6.2000, 106.8166',
                '1',
            ]);
            fputcsv($handle, [
                'dev_1',
                'PT Dev Company',
                'Dev Office 1',
                '',
                'Bandung',
                '',
                '1',
            ]);

            // Reference notes (ignored by importer — rows starting with ===)
            fputcsv($handle, []);
            fputcsv($handle, ['=== REFERENCE NOTES (rows starting with === are ignored by the importer) ===']);
            fputcsv($handle, ['code', '— Must match the room/location-id used in devices. This is the upsert key.']);
            fputcsv($handle, ['coordinate', '— Format: "lat, lng" e.g. "-6.2000, 106.8166". Leave blank if not applicable.']);
            fputcsv($handle, ['is_active', '— Use 1 for active, 0 for inactive.']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // -------------------------------------------------------------------------
    // Import CSV  POST /admin/locations/import
    // -------------------------------------------------------------------------

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file   = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors  = [];
        $rowNum  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Skip header row
            if ($rowNum === 1) continue;

            // Skip blank lines and reference note rows
            if (empty(array_filter($row))) { $skipped++; continue; }
            if (isset($row[0]) && str_starts_with(trim($row[0]), '===')) { $skipped++; continue; }

            // Map columns
            [$code, $company_name, $name, $address, $city, $coordinate, $is_active]
                = array_pad(array_map('trim', $row), 7, '');

            // Validate required field
            if (empty($code)) {
                $errors[] = "Row {$rowNum}: 'code' is required — row skipped.";
                $skipped++;
                continue;
            }

            // Validate coordinate format if provided
            if (!empty($coordinate)) {
                if (!preg_match('/^-?\d{1,2}(\.\d+)?,\s*-?\d{1,3}(\.\d+)?$/', $coordinate)) {
                    $errors[] = "Row {$rowNum}: coordinate \"{$coordinate}\" is invalid. Use format \"lat, lng\" e.g. \"-6.2000, 106.8166\" — row skipped.";
                    $skipped++;
                    continue;
                }
            }

            // Normalise is_active — blank defaults to true
            $isActiveBool = $is_active === '' ? true : (bool)(int)$is_active;

            $data = [
                'company_name'    => $company_name ?: null,
                'name'            => $name ?: null,
                'address'         => $address ?: null,
                'city'            => $city ?: null,
                'coordinate'      => $coordinate ?: null,
                'is_active'       => $isActiveBool,
                'last_updated_at' => now(),
                'last_updated_by' => auth()->id(),
            ];

            // Upsert keyed on code
            $existing = Location::where('code', strtolower($code))->first();

            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                Location::create(array_merge(['code' => strtolower($code)], $data));
                $created++;
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
        ]);
    }

    // -------------------------------------------------------------------------
    // Export CSV  GET /admin/locations/export-csv
    // -------------------------------------------------------------------------

    public function exportCsv(Request $request)
    {
        $query = Location::with('updatedBy')->orderBy('code');

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool)(int)$request->is_active);
        }

        $filename = 'locations_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'code',
                'company_name',
                'name',
                'address',
                'city',
                'coordinate',
                'is_active',
            ]);

            $query->chunk(2000, function ($locations) use ($handle) {
                foreach ($locations as $loc) {
                    fputcsv($handle, [
                        $loc->code,
                        $loc->company_name,
                        $loc->name,
                        $loc->address,
                        $loc->city,
                        $loc->coordinate,
                        $loc->is_active ? '1' : '0',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}