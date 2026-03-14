/* ------------------------------------------------------------------------------
 * Custom JS - Clean Console (Select2 Warnings Gone)
 * ---------------------------------------------------------------------------- */

const getExportFilename = function (initial) {
    let d = new Date();
    let n = d.getTime();
    return `${initial}_${n}`;
};
