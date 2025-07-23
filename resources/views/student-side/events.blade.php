<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrgBit | Events</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .scrollable-container {
            max-height: 600px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .scrollable-container::-webkit-scrollbar {
            width: 8px;
        }
        .scrollable-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .scrollable-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .scrollable-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .event-card {
            width: 100%;
            max-width: 1045px;
            margin: 10px auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .filter-button.selected {
            background-color: #35408e;
            color: white;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-[#f6f6f6] font-sans">

    <!-- Header -->
    <header class="bg-[#35408e] w-full fixed top-0 left-0 right-0 z-50">
        <div class="w-[97%] mx-auto flex items-center justify-between px-4 py-4">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <a href="{{ route('student-side.home') }}" class="text-white text-2xl font-bold">
                    <img src="{{ asset('storage/logos/Logo.png') }}" alt="Logo" class="h-20 w-20" />
                </a>
            </div>

            <!-- Right: Navigation Links -->
            <nav class="flex items-center space-x-12">
                <a href="{{ route('student-side.home') }}" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                    <i class="fas fa-home text-xl"></i>
                    <span class="mt-1 text-sm">Home</span>
                </a>
                <a href="{{ route('student-side.organizations') }}" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                    <i class="fas fa-users text-xl"></i>
                    <span class="mt-1 text-sm">Organizations</span>
                </a>
                <a href="{{ route('student-side.events') }}" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                    <i class="fas fa-calendar-alt text-xl"></i>
                    <span class="mt-1 text-sm">Events</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                        <span class="mt-1 text-sm">Log Out</span>
                    </button>
                </form>
            </nav>
        </div>
        <div class="bg-[#FFD700] w-full h-2 fixed top-25 left-0 right-0 z-40">
            <!-- Content for the gold container goes here -->
        </div>
    </header>

    <!-- Body -->
    <div class="pt-36 px-8 flex flex-wrap gap-10 justify-center">
        <!-- Left Container: Events Section -->
        <div class="flex flex-col space-y-5">
            <!-- All Events Section -->
            <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[1090px] h-max">
                <div class="p-5">
                    <p class="text-xl font-bold">All Events ({{ $sortedEvents->count() }})</p>
                    <hr class="ml-2 my-4 border-t-2 border-gray-300 mt-2">
                    <div class="flex flex-wrap gap-x-16 gap-y-5">
                    @foreach($sortedEvents as $event)
                        <div class="h-[320px] w-[210px] border border-[#dddddd] shadow shadow-md rounded-md flex flex-col justify-between">
                            <!-- Image Section -->
                            <div class="ml-3 mt-2 w-[180px] h-[120px] border-black border rounded-md mb-2">
                                @if($event->media)
                                    <img src="{{ asset('storage/events/' . $event->media->file_name) }}" alt="Event Photo" class="w-full h-full object-cover rounded-md">
                                @else
                                    <img src="{{ asset('storage/events/default_event_image.jpg') }}" alt="Default Event Photo" class="w-full h-full object-cover rounded-md">
                                @endif
                            </div>
                            <div class="text-xs font-bold text-center justify-center items-center w-[150px] ml-6">
                                {{ $event->organization->org_name }}
                            </div>
                            <!-- Event Name and Button Section -->
                            <div class="p-3 flex flex-col items-center justify-end flex-grow">
                                <p class="text-center text-xs mb-3">{{ $event->event_name }}</p>
                                @if($event->has_joined)
                                    <button class="w-[150px] h-[30px] border border-black rounded-md bg-gray-500 text-white cursor-not-allowed" disabled>
                                        Already Joined
                                    </button>
                                @elseif($event->has_ended)
                                    <button class="w-[150px] h-[30px] border border-black rounded-md bg-gray-500 text-white cursor-not-allowed" disabled>
                                        Event Ended
                                    </button>
                                @else
                                    <button class="join-event-button w-[150px] h-[30px] border border-[#35408e] rounded-md bg-[#35408e] text-white hover:bg-[#2c3579] transition duration-200" data-event-id="{{ $event->event_id }}" data-org-id="{{ $event->organization->org_id }}">
                                        Join Event
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </div>
        </div>

        <!-- Right Container: About Section -->
        <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[400px] h-max p-6">
            <!-- About Section -->
            <p class="text-xl font-bold text-gray-800">About</p>
            <p class="text-gray-600 mt-2">
                Welcome to Orbit, your gateway to seamless organization management and event coordination.
            </p>
            <p class="text-gray-600 mt-2">
                Our platform empowers users to connect with organizations, explore opportunities, and participate in events that match their interests and aspirations.
            </p>
            <p class="text-gray-600 mt-2">
                Whether you're looking to expand your network, collaborate on projects, or stay updated with the latest events, Orbit makes it simple and engaging.
            </p>

            <!-- Contact Section -->
            <p class="text-lg font-bold text-gray-800 mt-6">Contact</p>
            <p class="text-gray-600">Have questions or need assistance? Reach out to us!</p><br>
            <p class="text-gray-600"><strong>Email:</strong> support@orgbit.com</p>
            <p class="text-gray-600"><strong>Contact:</strong> +63 912 345 6789</p>
        </div>
    </div>

    <!-- Join Event Modal -->
    <div id="joinEventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[24px] font-semibold">Join Event</h2>
                <button onclick="closeModal('joinEventModal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="eventDetails">
                <!-- Event details will be dynamically inserted here -->
            </div>
            <div class="mt-4">
                <label class="flex items-center">
                    <input type="checkbox" id="confirmationCheckbox" class="mr-2">
                    <span>I confirm that I want to join this event.</span>
                </label>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeModal('joinEventModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                <button id="confirmJoinButton" class="bg-[#35408e] text-white px-4 py-2 rounded hover:bg-[#2c3579]">Join Event</button>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto"></i>
            <p id="errorMessage" class="text-lg font-semibold mt-4">Error!</p>
            <button onclick="closeModal('errorModal')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Confirm
            </button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    // Function to close modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Function to show error modal
    function showErrorModal(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
    }

    // Function to update the button state for events
    function updateEventButtonState(buttonSelector, hasJoined, eventEnded) {
        const button = document.querySelector(buttonSelector);
        if (button) {
            if (eventEnded) {
                // Event has ended
                button.disabled = true;
                button.textContent = 'Event Ended';
                button.classList.remove('bg-[#35408e]');
                button.classList.add('bg-gray-500', 'cursor-not-allowed');
            } else if (hasJoined) {
                // Already joined the event
                button.disabled = true;
                button.textContent = 'Already Joined';
                button.classList.remove('bg-[#35408e]');
                button.classList.add('bg-gray-500', 'cursor-not-allowed');
            } else {
                // Not joined the event
                button.disabled = false;
                button.textContent = 'Join Event';
                button.classList.remove('bg-gray-500', 'cursor-not-allowed');
                button.classList.add('bg-[#35408e]');
            }
        }
    }

    // Check participation status for all events on page load
    document.querySelectorAll('.join-event-button').forEach(async (button) => {
        const eventId = button.dataset.eventId;

        try {
            const response = await fetch(`/check-event-participation/${eventId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            if (!response.ok) {
                throw new Error('Failed to check event participation status.');
            }

            const data = await response.json();
            console.log('Event Participation Response:', data); // Debugging log

            if (data.success) {
                // Update the button state for events
                updateEventButtonState(
                    `.join-event-button[data-event-id="${eventId}"]`,
                    data.hasJoined,
                    data.eventEnded // Pass eventEnded flag
                );
            }
        } catch (error) {
            console.error('Error checking event participation:', error);
        }
    });

    // Event Delegation for Join Event Buttons
    document.addEventListener('click', async (event) => {
        if (event.target.classList.contains('join-event-button')) {
            const eventId = event.target.dataset.eventId;

            try {
                // Fetch event details
                const eventResponse = await fetch(`/fetch-event-details/${eventId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (!eventResponse.ok) {
                    throw new Error('Failed to fetch event details.');
                }

                const event = await eventResponse.json();

                // Check if the event has ended
                const eventEnded = new Date(event.event_end_date) < new Date();

                if (eventEnded) {
                    showErrorModal('This event has already ended.');
                    return;
                }

                // Check if the user is a member of the organization hosting the event
                const membershipResponse = await fetch(`/check-organization-membership-for-event/${eventId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (!membershipResponse.ok) {
                    throw new Error('Failed to check membership status.');
                }

                const membershipData = await membershipResponse.json();

                if (!membershipData.isMember) {
                    // User is not a member, show an error message
                    showErrorModal('You must be a member of the organization to join this event.');
                    return;
                }

                // Populate the modal with event details
                const eventDetails = `
                    <hr>
                    <br>
                    <p class="text-lg font-semibold">${event.organization_name}</p>
                    <br>
                    <p class="text-lg font-semibold">Event: ${event.event_name}</p>
                    <br>
                    <p><strong>What:</strong> ${event.event_name}</p>
                    <p><strong>When:</strong> ${new Date(event.event_start_date).toLocaleString()} - ${new Date(event.event_end_date).toLocaleString()}</p>
                    <p><strong>Where:</strong> ${event.event_location}</p>
                `;
                document.getElementById('eventDetails').innerHTML = eventDetails;

                // Set the event ID in the modal
                document.getElementById('confirmJoinButton').dataset.eventId = eventId;

                // Open the modal
                document.getElementById('joinEventModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                showErrorModal(error.message || 'Failed to fetch event details. Please try again.');
            }
        }
    });

    // Confirm Join Event
    document.getElementById('confirmJoinButton').addEventListener('click', async () => {
        const eventId = document.getElementById('confirmJoinButton').dataset.eventId;
        const confirmationCheckbox = document.getElementById('confirmationCheckbox');

        if (!confirmationCheckbox.checked) {
            alert('Please confirm that you want to join this event.');
            return;
        }

        try {
            const response = await fetch(`/join-event/${eventId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to join the event.');
            }

            if (data.success) {
                // Update the button state
                updateEventButtonState(`.join-event-button[data-event-id="${eventId}"]`, true);

                // Show success modal
                showSuccessModal('You have successfully joined the event!');
                closeModal('joinEventModal');

                // Reload the page to reflect the updated state
                window.location.reload();
            } else {
                showErrorModal(data.message || 'Failed to join the event.');
            }
        } catch (error) {
            console.error('Error joining event:', error);
            showErrorModal('An error occurred while joining the event.');
        }
    });

    // Function to show success modal
    function showSuccessModal(message) {
        const successModal = document.createElement('div');
        successModal.id = 'successModal';
        successModal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
        successModal.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <i class="fas fa-check-circle w-12 h-12 text-green-500 mx-auto"></i>
                <p class="text-lg font-semibold mt-4">${message}</p>
                <button onclick="closeModal('successModal')" class="mt-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Confirm
                </button>
            </div>
        `;
        document.body.appendChild(successModal);
    }

    // Close success modal
    function closeSuccessModal() {
        const successModal = document.getElementById('successModal');
        if (successModal) {
            successModal.remove();
        }
    }
</script>
</body>
</html>