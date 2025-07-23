<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Event | OrgBit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 font-poppins">
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="bg-[#35408e] text-white w-[350px] p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center">
                <a href="/" class="text-white text-2xl font-bold">
                    <img src="{{ asset('storage/logos/Logo.png') }}" alt="Logo" class="h-150 w-150" />
                </a>
                <h1 class="text-[24px] tracking-[15px] font-light mb-6 mt-6 ml-5">
                    ORGBIT
                </h1>
            </div>
            <hr class="border-b border-customAccentColor mt-5 mb-10">
            <nav>
                <ul>
                    <li class="mb-10 flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="layout-dashboard" class="mr-5 w-8 h-8"></i>Dashboard
                        </a>
                    </li>
                    <li class="mb-10 flex items-center">
                        <div class="absolute left-0 h-10 w-1 bg-white rounded-r-lg"></div>
                        <a href="{{ route('admin.events') }}" class="flex items-center text-white underline text-[16px]" >
                            <i data-lucide="calendar" class="mr-5 w-8 h-8"></i>Events
                        </a>
                    </li>
                    <li class="mb-10 flex items-center">
                        <a href="{{ route('admin.members') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="users" class="mr-5 w-8 h-8"></i>Members
                        </a>
                    </li>
                    <li class="flex items-center">
                        <a href="{{ route('admin.settings') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="settings" class="mr-5 w-8 h-8"></i>Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <a href="{{ route('admin.settings') }}">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('storage/logos/' . $organization->org_file_path) }}" alt="Logo" class="h-14 w-14 rounded-full object-cover mr-3" />
                <p class="text-white text-[16px] font-semibold break-words w-[200px] mt-1">
                    @if(isset($organization))
                        {{ $organization->org_name }}
                    @else
                        Organization Not Found
                    @endif
                </p>
            </div>
        </a>
    </aside>

    <!-- Gold Container -->
    <div class="bg-[#FFD700] w-[5px] h-full"></div>

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-[#f6f6f6]">
            <h2 class="text-[40px] font-bold mb-5">CURRENT EVENT</h2>
            <div class="flex justify-between mb-5">
                <h2 class="text-[24px] font-semibold">On-Going Event</h2>
            </div>
            <div class="bg-[#ffffff] border-[#f6f6f6] border border-1 p-4 rounded-lg mb-6 shadow-md">
                <div class="flex gap-4">
                    <!-- Generate QR Attendance -->
                    <div onclick="openConfirmationModal()" class="bg-[#35408e] text-white p-4 rounded-lg shadow flex-1 text-center cursor-pointer transition-transform transform hover:scale-105 hover:bg-[#2e377b]">
                        <i data-lucide="qr-code" class="w-10 h-10 mx-auto mb-2"></i>
                        <p class="font-semibold">Generate</p>
                        <p class="text-sm">QR Attendance</p>
                    </div>
                    
                    <!-- Scan and Validate QR Code -->
                    <div onclick="openScanQRModal()" class="bg-[#35408e] text-white p-4 rounded-lg shadow flex-1 text-center cursor-pointer transition-transform transform hover:scale-105 hover:bg-[#2e377b]">
                        <i data-lucide="scan-qr-code" class="w-10 h-10 mx-auto mb-2"></i>
                        <p class="font-semibold">Scan and Validate</p>
                        <p class="text-sm">QR Code</p>
                    </div>
                    <!-- Disseminate Evaluation Link thru Email -->
                    <div onclick="openDisseminateConfirmationModal()" class="bg-[#35408e] text-white p-4 rounded-lg shadow flex-1 text-center cursor-pointer transition-transform transform hover:scale-105 hover:bg-[#2e377b]">
                        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2"></i>
                        <p class="font-semibold">Disseminate</p>
                        <p class="text-sm">Evaluation Link</p>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    <p>Event Name: {{ $event ? $event->event_name : 'N/A' }}</p>
                    <p>Event Location: {{ $event ? $event->event_location : 'N/A' }}</p>
                    <p>Start Time: {{ $event ? $event->event_start_date : 'N/A' }}</p>
                    <p>End Time: {{ $event ? $event->event_end_date : 'N/A' }}</p>
                </div>
            </div>

            <!-- Combined List of Registration and Attendees -->
            <div class="mt-6">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-[24px] font-semibold">List of Attendees</h2>
                    <input type="text" id="searchCombined" placeholder="Search registrations and attendees..." class="p-2 border border-gray-300 rounded-md w-[300px]">
                </div>
                <div class="overflow-x-auto h-[350px] overflow-y-auto bg-[#ffffff] border-[#f6f6f6] border border-1 rounded-md">
                    <table class="w-full table-fixed text-center">
                        <thead class="">
                            <tr>
                                <th class="p-3 w-[50px]">#</th>
                                <th class="p-3 w-[100px]">Student Name</th>
                                <th class="p-3 w-[100px]">Year</th>
                                <th class="p-3 w-[100px]">Course</th>
                                <th class="p-3 w-[100px]">College</th>
                                <th class="p-3 w-[100px]">Status</th>
                            </tr>
                        </thead>
                        <tbody id="combinedTableBody">
                            @foreach($filteredRegistrations as $index => $registration)
                                <tr class="border">
                                    <td class="p-3 w-[50px]">{{ $index + 1 }}</td>
                                    <td class="p-3 w-[100px]">{{ $registration->student_name }}</td>
                                    <td class="p-3 w-[100px]">{{ $registration->year }}</td>
                                    <td class="p-3 w-[100px]">{{ $registration->course }}</td>
                                    <td class="p-3 w-[100px]">{{ $registration->college }}</td>
                                    <td class="p-3 w-[100px]">Not Attended</td>
                                </tr>
                            @endforeach
                            @foreach($attendees as $index => $attendee)
                                <tr class="border">
                                    <td class="p-3 w-[50px]">{{ $index + 1 + count($filteredRegistrations) }}</td>
                                    <td class="p-3 w-[100px]">{{ $attendee->student_name }}</td>
                                    <td class="p-3 w-[100px]">{{ $attendee->year }}</td>
                                    <td class="p-3 w-[100px]">{{ $attendee->course }}</td>
                                    <td class="p-3 w-[100px]">{{ $attendee->college }}</td>
                                    <td class="p-3 w-[100px]">Attended</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Generate QR Attendance Modal -->
    <div id="generateQRModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <h2 class="text-[24px] font-bold mb-4">Generate QR Attendance</h2>
            <p id="generateQRModalMessage" class="mb-4">Generating QR codes and sending emails...</p>
            <div id="generateQRLoadingSpinner" class="flex justify-center mb-4">
                <i data-lucide="loader" class="w-8 h-8 animate-spin"></i>
            </div>
            <div class="flex justify-end space-x-4">
                <button onclick="closeGenerateQRModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- Scan QR Code Modal -->
    <div id="scanQRModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[90%] max-w-[800px]">
            <h2 class="text-[24px] font-bold mb-4">Scan QR Code</h2>
            
            <!-- Scanner Container -->
            <div id="qr-scanner" class="w-full h-[600px] bg-gray-200 mb-4 relative overflow-hidden">
                <!-- Scanner will be rendered here -->
            </div>
            
            <!-- Scanner Status -->
            <p id="scannerStatus" class="text-center mb-4">Scanning...</p>
            
            <!-- Close Button -->
            <div class="flex justify-end">
                <button onclick="closeScanQRModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- Already Scanned Modal -->
    <div id="alreadyScannedModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 mx-auto text-yellow-500"></i>
            <h2 class="text-[24px] font-bold mb-4">Already Scanned</h2>
            <p class="mb-4">This QR code has already been scanned and marked as attended.</p>
            <button onclick="closeAlreadyScannedModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>

    <!-- Successfully Scanned Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <i data-lucide="check-circle" class="w-12 h-12 mx-auto text-green-500"></i>
            <h2 class="text-[24px] font-bold mb-4">Successfully Scanned</h2>
            <p class="mb-4">The QR code has been successfully scanned and marked as attended.</p>
            <button onclick="closeSuccessModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <i data-lucide="x-circle" class="w-12 h-12 mx-auto text-red-500"></i>
            <h2 class="text-[24px] font-bold mb-4">Error</h2>
            <p id="errorMessage" class="mb-4">An error occurred while scanning the QR code.</p>
            <button onclick="closeErrorModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>

    <!-- Disseminate Evaluation Link Modal -->
    <div id="disseminateEvaluationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <h2 class="text-[24px] font-bold mb-4">Disseminate Evaluation Link</h2>
            <p id="disseminateEvaluationModalMessage" class="mb-4">Sending evaluation links...</p>
            <div id="disseminateEvaluationLoadingSpinner" class="flex justify-center mb-4">
                <i data-lucide="loader" class="w-8 h-8 animate-spin"></i>
            </div>
            <div class="flex justify-end space-x-4">
                <button onclick="closeDisseminateEvaluationModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Dissemination -->
    <div id="disseminateConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <h2 class="text-[24px] font-bold mb-4">Confirm Dissemination</h2>
            <p class="mb-4">Are you sure you want to send evaluation links to all attendees?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeDisseminateConfirmationModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button onclick="proceedWithDissemination()" class="bg-blue-500 text-white px-4 py-2 rounded">Proceed</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for QR Generation -->
    <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-[400px] text-center">
            <h2 class="text-[24px] font-bold mb-4">Confirm Action</h2>
            <p class="mb-4">Are you sure you want to generate QR codes and send emails to all registrants?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeConfirmationModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button onclick="proceedWithQRGeneration()" class="bg-blue-500 text-white px-4 py-2 rounded">Proceed</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let html5QrCode;

        // Open Scan QR Modal
        function openScanQRModal() {
            const modal = document.getElementById('scanQRModal');
            modal.classList.remove('hidden');
            startQRScanner();
        }

        // Close Scan QR Modal
        function closeScanQRModal() {
            const modal = document.getElementById('scanQRModal');
            modal.classList.add('hidden');
            stopQRScanner();
        }

        // Open Already Scanned Modal
        function openAlreadyScannedModal() {
            const modal = document.getElementById('alreadyScannedModal');
            modal.classList.remove('hidden');
        }

        // Close Already Scanned Modal
        function closeAlreadyScannedModal() {
            const modal = document.getElementById('alreadyScannedModal');
            modal.classList.add('hidden');
        }

        // Open Success Modal
        function openSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.classList.remove('hidden');
        }

        // Close Success Modal
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.classList.add('hidden');
        }

        // Open Error Modal
        function openErrorModal(message) {
            const modal = document.getElementById('errorModal');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message; // Set the error message
            modal.classList.remove('hidden');
        }

        // Close Error Modal
        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            modal.classList.add('hidden');
        }

        // Start QR Scanner
        function startQRScanner() {
            const scannerContainer = document.getElementById('qr-scanner');
            scannerContainer.innerHTML = ''; // Clear previous scanner instance

            html5QrCode = new Html5Qrcode("qr-scanner");

            const qrCodeSuccessCallback = (decodedText) => {
                stopQRScanner();
                validateQRCode(decodedText);
            };

            const config = { fps: 10, qrbox: { width: 250, height: 250 } }; // Adjusted for mobile

            html5QrCode.start(
                { facingMode: "environment" }, // Use the rear camera
                config,
                qrCodeSuccessCallback
            ).catch((err) => {
                console.error("Unable to start QR scanner:", err);
                document.getElementById('scannerStatus').textContent = "Failed to start scanner. Please ensure your camera is accessible.";
            });
        }

        // Stop QR Scanner
        function stopQRScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    console.log("QR scanner stopped.");
                }).catch((err) => {
                    console.error("Failed to stop QR scanner:", err);
                });
            }
        }

        // Validate Scanned QR Code
        function validateQRCode(qrData) {
            fetch('/scan-validate-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_data: qrData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === "QR Code has already been validated.") {
                    openAlreadyScannedModal(); // Show "Already Scanned" modal
                } else if (data.message === "QR Code validated. Attendee confirmed.") {
                    openSuccessModal(); // Show "Successfully Scanned" modal
                } else {
                    openErrorModal(data.message || "An error occurred while validating the QR code."); // Show error modal
                }
            })
            .catch(error => {
                console.error('Error:', error);
                openErrorModal('An error occurred while validating the QR code.'); // Show error modal
            })
            .finally(() => {
                closeScanQRModal(); // Close the scanner modal
            });
        }

        // Open Confirmation Modal for QR Generation
        function openConfirmationModal() {
            const modal = document.getElementById('confirmationModal');
            modal.classList.remove('hidden');
        }

        // Close Confirmation Modal for QR Generation
        function closeConfirmationModal() {
            const modal = document.getElementById('confirmationModal');
            modal.classList.add('hidden');
        }

        // Proceed with QR Generation
        function proceedWithQRGeneration() {
            closeConfirmationModal(); // Close the confirmation modal
            openGenerateQRModal(); // Open the QR generation modal
        }

        // Open Generate QR Modal
        function openGenerateQRModal() {
            const modal = document.getElementById('generateQRModal');
            const modalMessage = document.getElementById('generateQRModalMessage');
            const loadingSpinner = document.getElementById('generateQRLoadingSpinner');

            // Show modal and start generating QR codes
            modal.classList.remove('hidden');
            modalMessage.textContent = 'Generating QR codes and sending emails...';
            loadingSpinner.classList.remove('hidden');

            // Fetch registrants and generate QR codes
            fetch('/generate-qr-attendance', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    event_id: {{ $event ? $event->event_id : 0 }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalMessage.textContent = 'QR codes generated and emails sent successfully!';
                } else {
                    modalMessage.textContent = data.message || 'Failed to generate QR codes. Please try again.';
                }
            })
            .catch(error => {
                modalMessage.textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            })
            .finally(() => {
                loadingSpinner.classList.add('hidden');
            });
        }

        // Close Generate QR Modal
        function closeGenerateQRModal() {
            document.getElementById('generateQRModal').classList.add('hidden');
        }

        // Open Confirmation Modal for Dissemination
        function openDisseminateConfirmationModal() {
            const modal = document.getElementById('disseminateConfirmationModal');
            modal.classList.remove('hidden');
        }

        // Close Confirmation Modal for Dissemination
        function closeDisseminateConfirmationModal() {
            const modal = document.getElementById('disseminateConfirmationModal');
            modal.classList.add('hidden');
        }

        // Proceed with Dissemination
        function proceedWithDissemination() {
            closeDisseminateConfirmationModal(); // Close the confirmation modal
            disseminateEvaluationLink(); // Call the dissemination function
        }

        // Disseminate Evaluation Link
        function disseminateEvaluationLink() {
            const modal = document.getElementById('disseminateEvaluationModal');
            const modalMessage = document.getElementById('disseminateEvaluationModalMessage');
            const loadingSpinner = document.getElementById('disseminateEvaluationLoadingSpinner');

            const eventId = {{ $event ? $event->event_id : 0 }};

            if (!eventId) {
                alert('No event selected.');
                return;
            }

            // Show modal and start sending evaluation links
            modal.classList.remove('hidden');
            modalMessage.textContent = 'Sending evaluation links...';
            loadingSpinner.classList.remove('hidden');

            // Fetch attendees and send evaluation links
            fetch('/disseminate-evaluation-link', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    event_id: eventId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalMessage.textContent = 'Evaluation links have been sent successfully!';
                } else {
                    modalMessage.textContent = data.message || 'Failed to send evaluation links. Please try again.';
                }
            })
            .catch(error => {
                modalMessage.textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            })
            .finally(() => {
                loadingSpinner.classList.add('hidden');
            });
        }

        // Close Disseminate Evaluation Modal
        function closeDisseminateEvaluationModal() {
            document.getElementById('disseminateEvaluationModal').classList.add('hidden');
        }

        // Search functionality for combined list
        document.getElementById('searchCombined').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#combinedTableBody tr');

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const year = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const course = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const college = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const status = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

                if (
                    name.includes(searchValue) ||
                    year.includes(searchValue) ||
                    course.includes(searchValue) ||
                    college.includes(searchValue) ||
                    status.includes(searchValue)
                ) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>