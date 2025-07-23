<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | OrgBit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <a href="{{ route('admin.events') }}" class="flex items-center text-white underline text-[16px]">
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
            <h2 class="text-[40px] font-bold mb-5">EVENTS</h2>

            <!-- Create a New Event -->
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-[24px] font-semibold">Create a New Event</h2>
                <a href="{{ route('admin.current-event') }}" class="bg-[#35408e] text-white px-4 py-2 rounded flex items-center">
                    <i data-lucide="calendar-sync" class="mr-2"></i> Current Event
                </a>
            </div>
            <div class="bg-[#ffffff] border border-1 border-[f6f6f6] p-4 rounded-lg mb-6 shadow-md">
                <form id="eventForm" enctype="multipart/form-data" class="grid grid-cols-2 gap-4" action="{{ route('events.store') }}" method="POST">
                    @csrf
                    <input type="text" name="event_name" id="event_name" placeholder="Event Name" class="p-2 border rounded w-full" required>
                    <div class="relative">
                        <input type="datetime-local" name="event_start_date" id="event_start_date" class="p-2 border rounded w-full" required>
                        <span id="startDatePlaceholder" class="absolute left-3 top-2.5 text-gray-400 pointer-events-none bg-white px-1">Start Date</span>
                    </div>
                    <input type="text" name="event_location" id="event_location" placeholder="Location" class="p-2 border rounded w-full" required>
                    <div class="relative">
                        <input type="datetime-local" name="event_end_date" id="event_end_date" class="p-2 border rounded w-full" required>
                        <span id="endDatePlaceholder" class="absolute left-3 top-2.5 text-gray-400 pointer-events-none bg-white px-1">End Date</span>
                    </div>
                    <input type="url" name="event_evaluation_link" id="event_evaluation_link" placeholder="Evaluation Link" class="p-2 border rounded w-full">
                    <input type="url" name="event_certification_link" id="event_certification_link" placeholder="Certification Link" class="p-2 border rounded w-full">
                    <div class="col-span-2">
                        <input type="file" name="event_image" id="event_image" class="p-2 border rounded w-full">
                    </div>
                    <button type="submit" class="bg-[#35408e] text-white p-2 rounded w-full col-span-2 hover:bg-[#2c3576] transition">
                        Create Event
                    </button>
                </form>
            </div>

            <!-- Success Modal -->
            <div id="successModal" class="hidden fixed inset-0 z-[200] flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md text-center">
                    <p id="successMessage" class="text-lg font-semibold text-green-600">Event created successfully!</p>
                </div>
            </div>

            <!-- Error Modal -->
            <div id="errorModal" class="hidden fixed inset-0 z-[200] flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md text-center">
                    <p id="errorMessage" class="text-lg font-semibold text-red-500">Failed to create event. Please try again!</p>
                    <button onclick="closeModal('errorModal')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">OK</button>
                </div>
            </div>

            <!-- Event Exists Modal -->
            <div id="eventExistsModal" class="hidden fixed inset-0 z-[200] flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md text-center">
                    <p id="eventExistsMessage" class="text-lg font-semibold text-red-500">Event already exists!</p>
                    <button onclick="closeModal('eventExistsModal')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">OK</button>
                </div>
            </div>

            <!-- Edit Event Modal -->
            <div id="editEventModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md w-full max-w-2xl relative">
                    <button onclick="closeModal('editEventModal')" class="absolute top-2 right-2 p-2 text-gray-500 hover:text-gray-700">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                    <h2 class="text-2xl font-bold mb-4">Edit Event</h2>
                    <form id="editEventForm" enctype="multipart/form-data" class="grid grid-cols-2 gap-4" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="text" name="event_name" id="edit_event_name" placeholder="Event Name" class="p-2 border rounded w-full" required>
                        <input type="datetime-local" name="event_start_date" id="edit_event_start_date" class="p-2 border rounded w-full" required>
                        <input type="text" name="event_location" id="edit_event_location" placeholder="Location" class="p-2 border rounded w-full" required>
                        <input type="datetime-local" name="event_end_date" id="edit_event_end_date" class="p-2 border rounded w-full" required>
                        <div class="col-span-2 grid grid-cols-2 gap-4">
                            <input type="url" name="event_evaluation_link" id="edit_event_evaluation_link" placeholder="Evaluation Link" class="p-2 border rounded w-full">
                            <input type="url" name="event_certification_link" id="edit_event_certification_link" placeholder="Certification Link" class="p-2 border rounded w-full">
                        </div>
                        <div class="col-span-2">
                            <label for="edit_event_image" class="block text-sm font-medium text-gray-700">Upload File</label>
                            <input type="file" name="event_image" id="edit_event_image" class="p-2 border rounded w-full">
                        </div>
                        <button type="submit" class="bg-black text-white p-2 rounded w-full col-span-2 hover:bg-gray-800 transition">
                            Update Event
                        </button>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteConfirmationModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md w-full max-w-2xl text-center relative">
                    <button onclick="closeModal('deleteConfirmationModal')" class="absolute top-2 right-2 p-2 text-gray-500 hover:text-gray-700">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                    <p class="text-lg font-semibold text-red-500">Are you sure you want to delete this event?</p>
                    <div class="mt-4">
                        <button onclick="confirmDelete()" class="px-4 py-2 bg-red-500 text-white rounded">Yes, Delete</button>
                        <button onclick="closeModal('deleteConfirmationModal')" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- List of Events with Search Bar -->
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-[24px] font-semibold">List of Events</h2>
                <div class="flex items-center space-x-2">
                    <input type="text" id="searchBar" placeholder="Search events..." class="p-2 border rounded w-64">
                </div>
            </div>

            <!-- Events Table -->
            <div class="overflow-x-auto h-[320px] overflow-y-auto border border-1 rounded-lg shadow-md bg-[#ffffff] border-[#f6f6f6]">
                <table class="w-full table-fixed text-center">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="p-3 border w-[200px]">Event Name</th>
                            <th class="p-3 border w-[200px]">Event Location</th>
                            <th class="p-3 border w-[180px]">Event Start Date</th>
                            <th class="p-3 border w-[180px]">Event End Date</th>
                            <th class="p-3 border w-[100px]">Total Participants</th>
                            <th class="p-3 border w-[150px]">Evaluation Form</th>
                            <th class="p-3 border w-[150px]">Certificates Folder</th>
                            <th class="p-3 border w-[100px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr class="border">
                                <td class="p-3 border w-[200px] break-words">{{ $event->event_name }}</td>
                                <td class="p-3 border w-[200px] break-words">{{ $event->event_location }}</td>
                                <td class="p-3 border w-[180px]">{{ \Carbon\Carbon::parse($event->event_start_date)->format('M d, Y h:i A') }}</td>
                                <td class="p-3 border w-[180px]">{{ \Carbon\Carbon::parse($event->event_end_date)->format('M d, Y h:i A') }}</td>
                                <td class="p-3 border w-[100px]">{{ $event->registrants_count ?? 0 }}</td>
                                <td class="p-3 border w-[150px]">
                                    <a href="{{ $event->event_evaluation_link }}" class="text-blue-500 underline" target="_blank">View Evaluation</a>
                                </td>
                                <td class="p-3 border w-[150px]">
                                    <a href="{{ $event->event_certification_link }}" class="text-blue-500 underline" target="_blank">View Certification</a>
                                </td>
                                <td class="space-x-2 pt-2">
                                    <button onclick="openEditModal({{ $event->event_id }}, '{{ $event->event_name }}', '{{ $event->event_start_date }}', '{{ $event->event_end_date }}', '{{ $event->event_location }}', '{{ $event->event_evaluation_link }}', '{{ $event->event_certification_link }}')" class="text-blue-500 hover:text-blue-700">
                                        <i data-lucide="edit" class="w-5 h-5"></i>
                                    </button>
                                    <button onclick="openDeleteModal({{ $event->event_id }})" class="text-red-500 hover:text-red-700">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function showSuccessModal(message) {
            closeModal('editEventModal');
            closeModal('deleteConfirmationModal');
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }

        function showErrorModal(message) {
            closeModal('editEventModal');
            closeModal('deleteConfirmationModal');
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        function showEventExistsModal() {
            closeModal('editEventModal');
            closeModal('deleteConfirmationModal');
            document.getElementById('eventExistsModal').classList.remove('hidden');
        }

        document.getElementById('eventForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const eventName = document.getElementById('event_name').value;
            const response = await fetch(`/check-event-name?event_name=${eventName}`);
            const data = await response.json();

            if (data.exists) {
                showEventExistsModal();
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to create event.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessModal(data.message);
                } else {
                    showErrorModal(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal(error.message);
            });
        });

        function handleDateTimePlaceholders() {
            const startDateInput = document.getElementById('event_start_date');
            const endDateInput = document.getElementById('event_end_date');
            const startDatePlaceholder = document.getElementById('startDatePlaceholder');
            const endDatePlaceholder = document.getElementById('endDatePlaceholder');

            if (startDateInput.value) {
                startDatePlaceholder.classList.add('hidden');
            }
            if (endDateInput.value) {
                endDatePlaceholder.classList.add('hidden');
            }

            startDateInput.addEventListener('input', () => {
                if (startDateInput.value) {
                    startDatePlaceholder.classList.add('hidden');
                } else {
                    startDatePlaceholder.classList.remove('hidden');
                }
            });

            endDateInput.addEventListener('input', () => {
                if (endDateInput.value) {
                    endDatePlaceholder.classList.add('hidden');
                } else {
                    endDatePlaceholder.classList.remove('hidden');
                }
            });
        }

        document.getElementById('searchBar').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const eventName = row.querySelector('td').textContent.toLowerCase();
                if (eventName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function openEditModal(eventId, eventName, eventStartDate, eventEndDate, eventLocation, evaluationLink, certificationLink) {
            document.getElementById('edit_event_name').value = eventName;
            document.getElementById('edit_event_start_date').value = eventStartDate.replace(' ', 'T');
            document.getElementById('edit_event_end_date').value = eventEndDate.replace(' ', 'T');
            document.getElementById('edit_event_location').value = eventLocation;
            document.getElementById('edit_event_evaluation_link').value = evaluationLink;
            document.getElementById('edit_event_certification_link').value = certificationLink;
            document.getElementById('editEventForm').action = `/events/${eventId}`;
            document.getElementById('editEventModal').classList.remove('hidden');
        }

        document.getElementById('editEventForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const eventName = document.getElementById('edit_event_name').value;
            const eventId = this.action.split('/').pop();
            const response = await fetch(`/check-event-name?event_name=${eventName}&exclude_event_id=${eventId}`);
            const data = await response.json();

            if (data.exists) {
                showEventExistsModal();
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT',
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Validation failed');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessModal('Event updated successfully!');
                } else {
                    showErrorModal(data.message || 'Failed to update event.');
                }
            })
            .catch(error => {
                console.error('Error updating event:', error);
                showErrorModal('An error occurred while updating the event.');
            });
        });

        let eventIdToDelete = null;

        function openDeleteModal(eventId) {
            eventIdToDelete = eventId;
            document.getElementById('deleteConfirmationModal').classList.remove('hidden');
        }

        function confirmDelete() {
            if (eventIdToDelete) {
                fetch(`/events/${eventIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showSuccessModal('Event deleted successfully!');
                    } else {
                        showErrorModal('Failed to delete event.');
                    }
                })
                .catch(error => {
                    console.error('Error deleting event:', error);
                    showErrorModal('An error occurred while deleting the event.');
                });
            }
        }

        document.addEventListener('DOMContentLoaded', handleDateTimePlaceholders);
        lucide.createIcons();
    </script>
</body>
</html>