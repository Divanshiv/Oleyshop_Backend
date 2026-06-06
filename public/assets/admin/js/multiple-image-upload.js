$(document).ready(function () {
    let hasFileSizeError = false;

    function getCount($picker) {
        const existingInputs = $picker.find('input[name="existing_identity_images[]"]');
        const existingCount = existingInputs.filter(function() {
            return $(this).val().trim() !== "";
        }).length;

        const newInputs = $picker.find('input[name="identity_images[]"], input[name="images[]"]');
        const newCount = newInputs.filter(function() {
            return $(this).val().trim() !== "";
        }).length;

        const total = existingCount + newCount;

        const maxCount = parseInt($picker.data("max-count")) || 4;

        if (total < maxCount) {
            $picker.find(".spartan--item--wrapper:last-child").removeClass("d-none");
        } else {
            $picker.find(".spartan--item--wrapper:last-child").addClass("d-none");
        }
    }


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
                hasFileSizeError = false;
            });
        });
    }


    window.hasFileSizeError = () => hasFileSizeError;

    window.validateRequiredImages = function () {
        let isValid = true;

        $(".multi_image_picker[data-required='true']").each(function () {
            const $picker = $(this);
            const hasImage = $picker.find(".spartan_item").length > 0 || $picker.find('input[type="file"]').val();

            if (!hasImage) {
                isValid = false;
                toastr.error($picker.data("required-msg") || "This field is required");

                $("html, body").animate(
                    { scrollTop: $picker.offset().top - 120 },
                    600
                );

                return false;
            }
        });

        return isValid;
    };

    function checkNavOverflow($picker) {
        try {
            const $btnNext = $picker.find(".imageSlide_next");
            const $btnPrev = $picker.find(".imageSlide_prev");
            const isRTL = $("html").attr("dir") === "rtl";
            const scrollWidth = $picker[0].scrollWidth;
            const clientWidth = $picker[0].clientWidth;
            const scrollLeft = $picker.scrollLeft();

            if (isRTL) {
                const maxScrollLeft = scrollWidth - clientWidth;
                const scrollRight = maxScrollLeft - scrollLeft;
                $btnNext.toggle(scrollLeft > 0);
                $btnPrev.toggle(scrollRight > 1);
            } else {
                $btnNext.toggle(
                    scrollWidth > clientWidth &&
                    scrollLeft + clientWidth < scrollWidth
                );
                $btnPrev.toggle(scrollLeft > 1);
            }
        } catch (error) {
            console.error("Error checking nav overflow:", error);
        }
    }

    function setAcceptForAllInputs() {
        const allowedExtensions = '.png,.jpg,.jpeg,.gif,.webp';
        $('.multi_image_picker input[type=file]').each(function() {
            $(this).attr('accept', allowedExtensions);
        });
    }
    setAcceptForAllInputs();


    // --- Initialize pickers ---
    $(".multi_image_picker").each(function () {
        const $picker = $(this);
        const ratio = $picker.data("ratio");
        const fieldName = $picker.data("field-name");
        const maxCount = $picker.data("max-count") || 4;
        const singleFileMaxLength = $picker.data('maxlength');

        sizeCheckAllSpartanUploaders();

        $picker.spartanMultiImagePicker({
            fieldName,
            maxCount,
            singleFileMaxLength,
            rowHeight: "100px",
            groupClassName: "spartan--item--wrapper",
            maxFileSize: '',
            allowedExt: "webp|jpg|jpeg|png|gif",
            dropFileLabel: `
                <div class="drop-label text-center">
                    <span class="text-primary fs-16"><i class="tio-camera-enhance"></i></span>
                    <h6 class="fs-10 mt-1 fw-medium lh-base text-center text-body">Add Image</h6>
                </div>`,
            placeholderImage: { image: '', width: "30px", height: "30px" },
            onAddRow() {
                setAcceptForAllInputs()
                getCount($picker);
                checkNavOverflow($picker);
                setAspectRatio($picker, ratio);
                hasFileSizeError = false;
            },
            onRemoveRow() {
                getCount($picker);
                checkNavOverflow($picker);
                setAspectRatio($picker, ratio);
            },
            onSizeErr() {
                hasFileSizeError = true;
                toastr.error('File size is larger than allowed');
            },
        });

        checkNavOverflow($picker);
        setAspectRatio($picker, ratio);


        $picker.find(".imageSlide_next").click(function () {
            const scrollWidth = $picker.find(".spartan_item_wrapper").outerWidth(true);
            $picker.animate({ scrollLeft: $picker.scrollLeft() + scrollWidth }, 300, function () {
                checkNavOverflow($picker);
            });
        });

        $picker.find(".imageSlide_prev").click(function () {
            const scrollWidth = $picker.find(".spartan_item_wrapper").outerWidth(true);
            $picker.animate({ scrollLeft: $picker.scrollLeft() - scrollWidth }, 300, function () {
                checkNavOverflow($picker);
            });
        });

        function setAspectRatio($picker, ratio) {
            if (ratio) {
                $picker.find(".file_upload").css("aspect-ratio", ratio);
            }
        }
    });

    let resizeTimeout;
    $(window).on("resize", function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            $(".multi_image_picker").each(function () {
                checkNavOverflow($(this));
            });
        }, 200);
    });

    $(".multi_image_picker").on("scroll", function () {
        checkNavOverflow($(this));
    });
});
