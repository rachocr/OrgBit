<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members | OrgBit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
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
                        <a href="{{ route('admin.events') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="calendar" class="mr-5 w-8 h-8"></i>Events
                        </a>
                    </li>
                    <li class="mb-10 flex items-center">
                        <div class="absolute left-0 h-10 w-1 bg-white rounded-r-lg"></div>
                        <a href="{{ route('admin.members') }}" class="flex items-center text-white underline text-[16px]">
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
            <h2 class="text-[40px] font-bold">MEMBERS</h2>

            <!-- Member Applications Table -->
            <div class="mt-6">
                <h3 class="mb-5 text-[24px] font-semibold">Member Applications</h3>
                <div class="overflow-x-auto h-[300px] overflow-y-auto border border-gray-300 bg-[#ffffff] shadow-md rounded-lg">
                    <table class="w-full table-fixed text-center">
                        <thead>
                            <tr class="border-b">
                                <th class="p-2 w-[300px]">Student Email</th>
                                <th class="p-2 w-[200px]">Name</th>
                                <th class="p-2 w-[100px]">Year</th>
                                <th class="p-2 w-[200px]">College</th>
                                <th class="p-2 w-[200px]">Course</th>
                                <th class="p-2 w-[200px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($applications as $application)
                            <tr class="border-b">
                                <td class="p-2">{{ $application->student?->student_email }}</td>
                                <td class="p-2">{{ $application->student?->student_name }}</td>
                                <td class="p-2">{{ $application->student?->student_year }}</td>
                                <td class="p-2">{{ $application->student?->college->college_name }}</td>
                                <td class="p-2">
                                    {{ $application->student?->course?->course_name }} 
                                    @if ($application->student?->course?->specialization)
                                        - {{ $application->student->course->specialization }}
                                    @endif
                                </td>
                                <td class="p-2 flex space-x-2 items-center justify-center">
                                    @if (!$application->student)
                                        <span class="text-red-500">Student not found (ID: {{ $application->student_id }})</span>
                                    @else
                                        <button onclick="openApproveModal({{ $application->member_id }})" class="bg-green-500 text-white px-4 py-2 rounded flex items-center justify-center space-x-2">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                            <span>Approve</span>
                                        </button>
                                        <button onclick="openRejectModal({{ $application->member_id }})" class="bg-red-500 text-white px-4 py-2 rounded flex items-center justify-center space-x-2">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                            <span>Reject</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-2 text-center">No applications found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- List of Members Table -->
            <div class="mt-6">
                <h3 class="text-[24px] font-semibold mb-5">List of Members</h3>
                <div class="overflow-x-auto h-[350px] overflow-y-auto border border-gray-300 bg-[#ffffff] shadow-md rounded-lg">
                    <table class="w-full table-fixed text-center">
                        <thead>
                            <tr class="border-b">
                                <th class="p-2 w-[300px]">Student Email</th>
                                <th class="p-2 w-[200px]">Name</th>
                                <th class="p-2 w-[100px]">Year</th>
                                <th class="p-2 w-[200px]">College</th>
                                <th class="p-2 w-[200px]">Course</th>
                                <th class="p-2 w-[100px]">Position</th>
                                <th class="p-2 w-[200px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <tr class="border-b">
                                    <td class="p-2 w-[300px]">{{ $member->student?->student_email }}</td>
                                    <td class="p-2 w-[200px]">{{ $member->student?->student_name }}</td>
                                    <td class="p-2 w-[100px]">{{ $member->student?->student_year }}</td>
                                    <td class="p-2 w-[200px]">{{ $member->student?->college->college_name }}</td>
                                    <td class="p-2 w-[200px]">
                                        {{ $member->student?->course?->course_name }} 
                                        @if ($member->student?->course?->specialization)
                                            - {{ $member->student->course->specialization }}
                                        @endif
                                    </td>
                                    <td class="p-2 w-[100px]">
                                        @if ($member->position_id == 2)
                                            Member
                                        @elseif ($member->position_id == 3)
                                            Committee
                                        @elseif ($member->position_id == 4)
                                            Executive
                                        @endif
                                    </td>
                                    <td class="p-2 flex space-x-2 items-center justify-center">
                                        <button onclick="openEditModal({{ $member->member_id }}, {{ $member->position_id }})" class="bg-blue-500 text-white px-3 py-2 rounded flex items-center justify-center space-x-2">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button onclick="openRemoveModal({{ $member->member_id }})" class="bg-red-500 text-white px-3 py-2 rounded flex items-center justify-center space-x-2">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            <span>Remove</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-2 text-center">No members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
            <h3 class="text-xl font-bold mb-4">Approve Application</h3>
            <p>Are you sure you want to approve this application?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeApproveModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button onclick="confirmApprove()" class="bg-green-500 text-white px-4 py-2 rounded">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
            <h3 class="text-xl font-bold mb-4">Reject Application</h3>
            <p>Are you sure you want to reject this application?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeRejectModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button onclick="confirmReject()" class="bg-red-500 text-white px-4 py-2 rounded">Reject</button>
            </div>
        </div>
    </div>

    <!-- Remove Modal -->
    <div id="removeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
            <h3 class="text-xl font-bold mb-4">Remove Member</h3>
            <p>Are you sure you want to remove this member?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeRemoveModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button onclick="confirmRemove()" class="bg-red-500 text-white px-4 py-2 rounded">Remove</button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
            <h3 class="text-xl font-bold mb-4">Edit Member Position</h3>
            <form id="editForm">
                <div class="mb-4">
                    <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                    <select id="position" name="position" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="2">Member</option>
                        <option value="3">Committee</option>
                        <option value="4">Executive</option>
                    </select>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="button" onclick="confirmEdit()" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
            <h3 class="text-xl font-bold mb-4">Success</h3>
            <p id="successMessage"></p>
            <div class="mt-6 flex justify-end">
                <button onclick="closeSuccessModal()" class="bg-green-500 text-white px-4 py-2 rounded">OK</button>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[400px]">
            <h3 class="text-xl font-bold mb-4">Error</h3>
            <p id="errorMessage"></p>
            <div class="mt-6 flex justify-end">
                <button onclick="closeErrorModal()" class="bg-red-500 text-white px-4 py-2 rounded">OK</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let currentMemberId = null;
        let currentPositionId = null;

        // Approve Modal Functions
        function openApproveModal(memberId) {
            currentMemberId = memberId;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function confirmApprove() {
            fetch(`/organization/members/approve/${currentMemberId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    closeApproveModal();
                    showSuccessModal('Application approved successfully.');
                    setTimeout(() => location.reload(), 1500); // Reload after 1.5 seconds
                } else {
                    closeApproveModal();
                    showErrorModal('Failed to approve the application.');
                }
            }).catch(error => {
                closeApproveModal();
                showErrorModal('An error occurred while approving the application.');
            });
        }

        // Reject Modal Functions
        function openRejectModal(memberId) {
            currentMemberId = memberId;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function confirmReject() {
            fetch(`/organization/members/reject/${currentMemberId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    closeRejectModal();
                    showSuccessModal('Application rejected successfully.');
                    setTimeout(() => location.reload(), 1500); // Reload after 1.5 seconds
                } else {
                    closeRejectModal();
                    showErrorModal('Failed to reject the application.');
                }
            }).catch(error => {
                closeRejectModal();
                showErrorModal('An error occurred while rejecting the application.');
            });
        }

        // Remove Modal Functions
        function openRemoveModal(memberId) {
            currentMemberId = memberId;
            document.getElementById('removeModal').classList.remove('hidden');
        }

        function closeRemoveModal() {
            document.getElementById('removeModal').classList.add('hidden');
        }

        function confirmRemove() {
            fetch(`/organization/members/remove/${currentMemberId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    closeRemoveModal();
                    showSuccessModal('Member removed successfully.');
                    setTimeout(() => location.reload(), 1500); // Reload after 1.5 seconds
                } else {
                    closeRemoveModal();
                    showErrorModal('Failed to remove the member.');
                }
            }).catch(error => {
                closeRemoveModal();
                showErrorModal('An error occurred while removing the member.');
            });
        }

        // Edit Modal Functions
        function openEditModal(memberId, positionId) {
            currentMemberId = memberId;
            currentPositionId = positionId;
            document.getElementById('position').value = positionId;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function confirmEdit() {
            const newPositionId = document.getElementById('position').value;
            fetch(`/organization/members/update-position/${currentMemberId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ position_id: newPositionId }),
            }).then(response => {
                if (response.ok) {
                    closeEditModal();
                    showSuccessModal('Member position updated successfully.');
                    setTimeout(() => location.reload(), 1500); // Reload after 1.5 seconds
                } else {
                    closeEditModal();
                    showErrorModal('Failed to update member position.');
                }
            }).catch(error => {
                closeEditModal();
                showErrorModal('An error occurred while updating member position.');
            });
        }

        // Success Modal Functions
        function showSuccessModal(message) {
            document.getElementById('successMessage').innerText = message;
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        // Error Modal Functions
        function showErrorModal(message) {
            document.getElementById('errorMessage').innerText = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }
    </script>
</body>
</html>