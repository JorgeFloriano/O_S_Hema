<div>
    @if (session()->has('message'))
        <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1080">
            <div id="liveToast" class="toast align-items-center bg-white border border-primary shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fa fa-info-circle me-3 text-primary fs-5"></i>
                        <span class="text-dark">{{ session()->get('message') }}</span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toastElement = document.getElementById('liveToast');
                if (toastElement) {
                    var toast = new bootstrap.Toast(toastElement, {
                        delay: 4000 // Ele some após 4 segundos
                    });
                    toast.show();
                }
            });
        </script>
    @endif
</div>