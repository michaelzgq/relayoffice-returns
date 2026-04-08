document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelectorAll(".upload-file").length) {
        initFileUpload();
        checkPreExistingImages();
        handleFormSubmit();
    }
});

function initFileUpload() {
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("upload-file-input")) {
            handleFileChange(e.target, e.target.files[0]);
        }
    });
}

function handleFileChange(input, file) {
    const card = input.closest(".upload-file");
    const textbox = card.querySelector(".upload-file-textbox");
    const imgElement = card.querySelector(".upload-file-img");
    const removeBtn = card.querySelector(".remove_btn");

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            if (textbox) textbox.style.display = "none";
            if (imgElement) {
                imgElement.src = e.target.result;
                imgElement.style.display = "block";
            }
            if (removeBtn) removeBtn.style.opacity = 1;

            card.removeAttribute("data-removed");
        };
        reader.readAsDataURL(file);
    }
}

function resetFileUpload(card) {
    const input = card.querySelector(".upload-file-input");
    const imgElement = card.querySelector(".upload-file-img");
    const textbox = card.querySelector(".upload-file-textbox");
    const removeBtn = card.querySelector(".remove_btn");
    const defaultSrc = imgElement?.dataset.defaultSrc || "";

    if (input) input.value = "";

    if (card.hasAttribute("data-removed")) {
        if (imgElement) {
            imgElement.style.display = "none";
            imgElement.src = "";
        }
        if (textbox) textbox.style.display = "block";
        if (removeBtn) removeBtn.style.opacity = 0;
        // $('#oldImage').val('');
    } else {
        if (defaultSrc) {
            if (imgElement) {
                imgElement.src = defaultSrc;
                imgElement.style.display = "block";
            }
            if (textbox) textbox.style.display = "none";
            if (removeBtn) removeBtn.style.opacity = 1;
        } else {
            if (imgElement) {
                imgElement.src = "";
                imgElement.style.display = "none";
            }
            if (textbox) textbox.style.display = "block";
            if (removeBtn) removeBtn.style.opacity = 0;
        }
    }
}

function checkPreExistingImages() {
    document.querySelectorAll(".upload-file").forEach(function (card) {
        const textbox = card.querySelector(".upload-file-textbox");
        const imgElement = card.querySelector(".upload-file-img");
        const removeBtn = card.querySelector(".remove_btn");

        const src = imgElement?.getAttribute("src");

        if (src && src !== window.location.href && src !== "") {
            if (textbox) textbox.style.display = "none";
            if (imgElement) imgElement.style.display = "block";
            if (removeBtn) removeBtn.style.opacity = 1;
        } else {
            if (textbox) textbox.style.display = "block";
            if (imgElement) imgElement.style.display = "none";
            if (removeBtn) removeBtn.style.opacity = 0;
        }
    });
}

function handleFormSubmit() {
    document.querySelectorAll("form").forEach(form => {
        const removedIds = new Set();

        form.addEventListener("click", function (e) {
            const removeBtn = e.target.closest(".remove_btn");
            const resetBtn = e.target.closest("button[type=reset]");

            if (removeBtn && form.contains(removeBtn)) {
                const card = removeBtn.closest(".upload-file");
                const imageId = card.getAttribute("data-image-id");
                if (imageId) {
                    removedIds.add(imageId);
                }
                card.setAttribute("data-removed", "true");
                resetFileUpload(card);
            }

            if (resetBtn && form.contains(resetBtn)) {
                removedIds.clear();
                form.querySelectorAll(".upload-file").forEach(card => {
                    card.removeAttribute("data-removed");
                    resetFileUpload(card);
                });

                // Reset oldImage value to original
                const oldImageInput = form.querySelector("#oldImage");
                if (oldImageInput) {
                    oldImageInput.value = oldImageInput.defaultValue;
                }

                const oldShopLogoInput = form.querySelector("#oldShopLogo");
                if (oldShopLogoInput) {
                    oldShopLogoInput.value = oldShopLogoInput.defaultValue;
                }

                const oldFavIconInput = form.querySelector("#oldFavIcon");
                if (oldFavIconInput) {
                    oldFavIconInput.value = oldFavIconInput.defaultValue;
                }

                const hiddenInput = form.querySelector("input[name=removed_images]");
                if (hiddenInput) hiddenInput.value = "";
            }
        });

        form.addEventListener("submit", function () {
            form.querySelectorAll(".upload-file").forEach(card => {
                const input = card.querySelector(".upload-file-input");
                const removed = card.hasAttribute("data-removed");
                if (removed)
                {
                    const hiddenInput = form.querySelector(`[name="old_${input.name}"]`);
                    if (hiddenInput) hiddenInput.value = "";
                }
            });

            const hiddenInput = form.querySelector("input[name=removed_images]");
            if (hiddenInput) {
                hiddenInput.value = Array.from(removedIds).join(",");
            }
        });
    });
}
