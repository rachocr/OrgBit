import "./bootstrap";

function startQRScanner() {
    const scannerContainer = document.getElementById("qr-scanner");
    scannerContainer.innerHTML = ""; // Clear previous scanner instance

    html5QrCode = new Html5Qrcode("qr-scanner");

    const qrCodeSuccessCallback = (decodedText) => {
        stopQRScanner();
        validateQRCode(decodedText);
    };

    // Increase the size of the qrbox (white scanning area)
    const config = {
        fps: 10,
        qrbox: { width: 500, height: 200 }, // Increase the width and height of the scanning box
    };

    html5QrCode
        .start(
            { facingMode: "environment" }, // Use the rear camera
            config,
            qrCodeSuccessCallback
        )
        .catch((err) => {
            console.error("Unable to start QR scanner:", err);
            document.getElementById("scannerStatus").textContent =
                "Failed to start scanner. Please ensure your camera is accessible.";
        });
}

function validateQRCode(qrData) {
    fetch("/scan-validate-qr", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({
            qr_data: qrData,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.message === "QR Code has already been validated.") {
                openAlreadyScannedModal(); // Show "Already Scanned" modal
            } else if (
                data.message === "QR Code validated. Attendee confirmed."
            ) {
                openSuccessModal(); // Show "Successfully Scanned" modal
            } else {
                openErrorModal(
                    data.message ||
                        "An error occurred while validating the QR code."
                ); // Show error modal
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            openErrorModal("An error occurred while validating the QR code."); // Show error modal
        })
        .finally(() => {
            closeScanQRModal(); // Close the scanner modal
        });
}
