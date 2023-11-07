@extends('2step::layout')

@section('title', __('2step::messages.title'))

@section('content')
<div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-12">
    <div class="relative bg-white px-6 pt-10 pb-9 shadow-xl mx-auto w-full max-w-lg rounded-2xl">
      <div class="mx-auto flex w-full max-w-md flex-col space-y-16">
        <div class="flex flex-col items-center justify-center text-center space-y-2">
          <div class="font-semibold text-3xl">
            <p>{{__('2step::messages.title')}}</p>
          </div>
          <div class="flex flex-row text-sm font-medium text-gray-400">
            <p>{{__('')}}</p>
          </div>
        </div>
  
        <div>
          <form method="POST">
            @csrf
            <div class="flex flex-col space-y-16">
              <div class="flex justify-center">
                <div class="grid grid-flow-col gap-4">
                @php $codeLength = auth()->user()->getTwoStepCodeLength(); @endphp
                @for ($i = 0; $i < $codeLength; $i++)
                    <input class="w-14 h-14 border border-blue-200 rounded-xl text-lg text-center bg-white focus:bg-gray-50 focus:ring-1 ring-blue-700 {{$i == $codeLength -1 ? 'last-input' : '' }}" type="text" name="code[]" id="code{{$i}}" maxlength="1" oninput="this.value = this.value.toUpperCase()">
                @endfor
                </div>
              </div>
  
              @if ($errors->any())
                <div class="text-red-500 text-center">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
              @endif

              <div class="flex flex-col space-y-5">
                <div>
                  <button class="flex flex-row items-center justify-center text-center w-full border rounded-xl outline-none py-5 bg-blue-700 border-none text-white text-sm shadow-sm disabled:opacity-50" id="confirm-btn" disabled>
                    {{__('2step::messages.confirm')}}
                  </button>
                </div>
                <div x-data="{ open: false }">
                <div class="flex flex-row items-center justify-center text-center text-sm font-medium space-x-1 text-gray-500">
                  <p>{{__('2step::messages.not_receive_code')}}</p> <a class="flex flex-row items-center text-blue-600" id="resend" rel="noopener noreferrer">
                    <span>{{__('2step::messages.resend')}}</span>
                    <svg id="spinner" class="animate-spin h-5 w-5 text-blue-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </a>
                </div>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <div x-data="{ open: false }" id="modal">
    <div x-show="open" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- ... -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Success
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="modal-text">
                                    Your message here.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="open = false">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@include('2step::scripts.input-handle')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<script>
  $("#resend").click(function(e) {
    e.preventDefault();

    $(this).addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
    $('#spinner').removeClass('hidden');

    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
        url: '/2step-resend',
        type: 'POST',
        success: function(response) {
            // Display a success modal
            document.getElementById('modal-title').textContent = 'Success';
            document.getElementById('modal-text').textContent = response.message;
            document.getElementById('modal').__x.$data.open = true;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Display an error modal
            document.getElementById('modal-title').textContent = 'Error';
            document.getElementById('modal-text').textContent = errorThrown;
            document.getElementById('modal').__x.$data.open = true;
        },
        complete: function() {
            $("#resend").removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
            $('#spinner').addClass('hidden');
        }
    });
});
</script>
@endpush