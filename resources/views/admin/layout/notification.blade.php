<div class="d-none">
  @if (session('success'))
    <button class="btn btn-light" id="sweet_success" msg="{{ session('success') }}" type="button">
      Launch <i class="icon-play3 ml-2"></i>
    </button>
    <script>
      $(document).ready(function() {
        $('#sweet_success').click();
      });
    </script>
  @endif
  @if (session('error'))
    <button class="btn btn-light" id="sweet_error" msg="{{ session('error') }}" type="button">
      Launch <i class="icon-play3 ml-2"></i>
    </button>
    <script>
      $(document).ready(function() {
        $('#sweet_error').click();
      });
    </script>
  @endif
</div>

<div class="position-absolute top-0 start-50 translate-middle-x mt-5">
  @if (session('errors'))
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="m-0">
        @foreach (session('errors')->all() as $error)
          <li>
            {{ $error }}
          </li>
        @endforeach
      </ul>
      <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
    </div>
  @endif
</div>
