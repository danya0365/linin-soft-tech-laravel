import Cropper from 'cropperjs';

/**
 * ImageCropUpload — Reusable image crop & upload module
 * Works with the <x-image-crop-upload> Blade component
 */
window.ImageCropUpload = (function () {
    const instances = {};

    function init() {
        document.addEventListener('DOMContentLoaded', function () {
            bindAllFileInputs();
        });

        // Re-bind on Turbolinks/Livewire navigation if needed
        document.addEventListener('turbolinks:load', function () {
            bindAllFileInputs();
        });
    }

    function bindAllFileInputs() {
        document.querySelectorAll('[id$="_wrapper"]').forEach(function (wrapper) {
            const uid = wrapper.id.replace('_wrapper', '');
            const fileInput = document.getElementById(uid + '_file_input');

            if (fileInput && !fileInput.dataset.cropBound) {
                fileInput.dataset.cropBound = 'true';
                fileInput.addEventListener('change', function (e) {
                    handleFileSelect(uid, e);
                });
            }
        });
    }

    function handleFileSelect(uid, e) {
        const file = e.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            openModal(uid, event.target.result, file.name);
        };
        reader.readAsDataURL(file);

        // Reset the visible file input so the same file can be re-selected
        e.target.value = '';
    }

    function openModal(uid, imageSrc, fileName) {
        const modal = document.getElementById(uid + '_modal');
        const cropImage = document.getElementById(uid + '_crop_image');

        if (!modal || !cropImage) return;

        // Store original file name
        instances[uid] = instances[uid] || {};
        instances[uid].fileName = fileName;

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Set image source
        cropImage.src = imageSrc;
        cropImage.style.display = 'block';

        // Destroy previous cropper if exists
        if (instances[uid].cropper) {
            instances[uid].cropper.destroy();
        }

        // Initialize Cropper.js
        instances[uid].cropper = new Cropper(cropImage, {
            viewMode: 1,
            dragMode: 'move',
            aspectRatio: 0, // Free by default
            autoCropArea: 0.9,
            responsive: true,
            restore: false,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: true,
            background: true,
        });

        // Reset ratio buttons — activate first one (Free)
        const ratioButtons = modal.querySelectorAll('.crop-ratio-btn');
        ratioButtons.forEach(function (btn, index) {
            if (index === 0) {
                activateRatioBtn(btn);
            } else {
                deactivateRatioBtn(btn);
            }
        });
    }

    function closeModal(uid) {
        const modal = document.getElementById(uid + '_modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        if (instances[uid] && instances[uid].cropper) {
            instances[uid].cropper.destroy();
            instances[uid].cropper = null;
        }
    }

    function setRatio(uid, ratio, btnEl) {
        if (!instances[uid] || !instances[uid].cropper) return;
        instances[uid].cropper.setAspectRatio(ratio === 0 ? NaN : ratio);

        // Update button styles
        const modal = document.getElementById(uid + '_modal');
        if (modal) {
            modal.querySelectorAll('.crop-ratio-btn').forEach(function (btn) {
                deactivateRatioBtn(btn);
            });
        }
        activateRatioBtn(btnEl);
    }

    function activateRatioBtn(btn) {
        btn.classList.remove('bg-gray-50', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300', 'border-gray-200', 'dark:border-gray-600');
        btn.classList.add('bg-indigo-100', 'dark:bg-indigo-900/50', 'text-indigo-700', 'dark:text-indigo-300', 'border-indigo-300', 'dark:border-indigo-600');
    }

    function deactivateRatioBtn(btn) {
        btn.classList.remove('bg-indigo-100', 'dark:bg-indigo-900/50', 'text-indigo-700', 'dark:text-indigo-300', 'border-indigo-300', 'dark:border-indigo-600');
        btn.classList.add('bg-gray-50', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300', 'border-gray-200', 'dark:border-gray-600');
    }

    function zoom(uid, value) {
        if (instances[uid] && instances[uid].cropper) {
            instances[uid].cropper.zoom(value);
        }
    }

    function rotate(uid, degree) {
        if (instances[uid] && instances[uid].cropper) {
            instances[uid].cropper.rotate(degree);
        }
    }

    function flip(uid, axis) {
        if (!instances[uid] || !instances[uid].cropper) return;
        const data = instances[uid].cropper.getData();
        if (axis === 'x') {
            instances[uid].cropper.scaleX(data.scaleX === -1 ? 1 : -1);
        } else {
            instances[uid].cropper.scaleY(data.scaleY === -1 ? 1 : -1);
        }
    }

    function resetCropper(uid) {
        if (instances[uid] && instances[uid].cropper) {
            instances[uid].cropper.reset();
        }
    }

    function confirm(uid) {
        if (!instances[uid] || !instances[uid].cropper) return;

        const wrapper = document.getElementById(uid + '_wrapper');
        const maxWidth = parseInt(wrapper.dataset.maxWidth) || 1920;
        const quality = parseFloat(wrapper.dataset.quality) || 0.8;

        const canvas = instances[uid].cropper.getCroppedCanvas({
            maxWidth: maxWidth,
            maxHeight: maxWidth,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) return;

        // Convert canvas to blob then to a File object
        canvas.toBlob(function (blob) {
            if (!blob) return;

            // Create a File from the Blob
            const originalName = instances[uid].fileName || 'cropped.jpg';
            const extension = originalName.split('.').pop().toLowerCase();
            const nameWithoutExt = originalName.replace(/\.[^.]+$/, '');
            const croppedFile = new File(
                [blob],
                nameWithoutExt + '_cropped.' + (extension === 'png' ? 'png' : 'jpg'),
                { type: blob.type }
            );

            // Use DataTransfer to set the hidden file input
            const hiddenInput = document.getElementById(uid + '_hidden_input');
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            hiddenInput.files = dt.files;

            // Remove required from visible input since we now have a file in hidden input
            const visibleInput = document.getElementById(uid + '_file_input');
            if (visibleInput) {
                visibleInput.removeAttribute('required');
            }

            // Show preview
            const previewWrapper = document.getElementById(uid + '_preview_wrapper');
            const previewImg = document.getElementById(uid + '_preview');
            const sizeInfo = document.getElementById(uid + '_size_info');
            const currentImg = document.getElementById(uid + '_current');
            const inputArea = document.getElementById(uid + '_input_area');

            if (previewImg) {
                previewImg.src = canvas.toDataURL('image/jpeg', quality);
            }
            if (previewWrapper) {
                previewWrapper.classList.remove('hidden');
            }
            if (currentImg) {
                currentImg.classList.add('hidden');
            }
            if (inputArea) {
                inputArea.classList.add('hidden');
            }
            if (sizeInfo) {
                const sizeMB = (blob.size / (1024 * 1024)).toFixed(2);
                sizeInfo.textContent = canvas.width + 'x' + canvas.height + 'px · ' + sizeMB + ' MB';
            }

            // Close modal
            closeModal(uid);
        }, 'image/jpeg', quality);
    }

    function remove(uid) {
        // Clear hidden input
        const hiddenInput = document.getElementById(uid + '_hidden_input');
        if (hiddenInput) {
            const dt = new DataTransfer();
            hiddenInput.files = dt.files;
        }

        // Hide preview, show input area
        const previewWrapper = document.getElementById(uid + '_preview_wrapper');
        const currentImg = document.getElementById(uid + '_current');
        const inputArea = document.getElementById(uid + '_input_area');

        if (previewWrapper) previewWrapper.classList.add('hidden');
        if (currentImg) currentImg.classList.remove('hidden');
        if (inputArea) inputArea.classList.remove('hidden');
    }

    // Initialize on load
    init();

    // Public API
    return {
        openModal: openModal,
        closeModal: closeModal,
        setRatio: setRatio,
        zoom: zoom,
        rotate: rotate,
        flip: flip,
        reset: resetCropper,
        confirm: confirm,
        remove: remove,
    };
})();
