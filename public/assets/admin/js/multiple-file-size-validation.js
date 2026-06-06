 (function($){
    "use strict";

    function toBytes(str) {
        const n = parseFloat(str) || 0;
        const u = (str || '').toString().toLowerCase().replace(/[^a-z]/g, '');
        const map = { b: 1, kb: 1024, mb: 1048576, gb: 1073741824 };
        return Math.round(n * (map[u] || 1));
    }

    function sizeCheckAllSpartanUploaders() {
        $('[data-total-max-size], [data-max-filesize]').each(function() {
            const $upload = $(this);

            if ($upload.data('spartan-initialized')) return;
            $upload.data('spartan-initialized', true);

            const maxTotal = toBytes($upload.data("total-max-size") || "10mb");
            const maxFile = toBytes($upload.data("max-filesize") || "2mb");
            let totalSize = 0;

            $upload.on("change", ".spartan_image_input", function(e) {
                const file = this.files[0];
                if (!file) return;

                if (file.size > maxFile) {
                    toastr.error(`Each file must be less than ${$upload.data("max-filesize")}`);
                    this.value = "";
                    e.stopImmediatePropagation();
                    return;
                }

                const $wrapper = $(this).closest('.spartan_item_wrapper');
                const oldSize = $wrapper.data('file-size') || 0;
                const newTotal = totalSize - oldSize + file.size;

                if (newTotal > maxTotal) {
                    toastr.error(`Total upload size exceeds ${$upload.data("total-max-size")}`);
                    this.value = "";
                    e.stopImmediatePropagation();
                    return;
                }

                totalSize = newTotal;
                $wrapper.data('file-size', file.size);
            });

            $upload.on("click", ".spartan_remove_row", function() {
                const $wrapper = $(this).closest('.spartan_item_wrapper');
                const size = $wrapper.data('file-size') || 0;
                totalSize -= size;
                if (totalSize < 0) totalSize = 0;
            });
        });
    }

    $(document).ready(function() {
        sizeCheckAllSpartanUploaders();
    });

    window.initSpartanUploader = sizeCheckAllSpartanUploaders;

})(jQuery);
