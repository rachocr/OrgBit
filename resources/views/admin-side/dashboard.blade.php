<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | OrgBit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="font-sans">
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
                        <div class="absolute left-0 h-10 w-1 bg-white rounded-r-lg"></div>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white underline text-[16px]">
                            <i data-lucide="layout-dashboard" class="mr-5 w-8 h-8"></i>Dashboard
                        </a>
                    </li>
                    <li class="mb-10 flex items-center">
                        <a href="{{ route('admin.events') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
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
            <h2 class="text-[40px] font-bold">DASHBOARD</h2>

            <!-- Stats Section -->
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="bg-[#ffffff] p-4 rounded-lg shadow-md flex items-center">
                    <i data-lucide="users" class="ml-5 mr-10 w-10 h-10"></i>
                    <div>
                        <p class="text-lg font-semibold">Total Members</p>
                        <p class="text-2xl">{{ $totalMembers }}</p>
                    </div>
                </div>
                <div class="bg-[#ffffff] p-4 rounded-lg shadow-md flex items-center">
                    <i data-lucide="calendar" class="ml-5 mr-10 w-10 h-10"></i>
                    <div>
                        <p class="text-lg font-semibold">Total Events</p>
                        <p class="text-2xl">{{ $stats->total_events ?? 0 }}</p>
                    </div>
                </div>
                <div class="bg-[#ffffff] p-4 rounded-lg shadow-md flex items-center">
                    <i data-lucide="file-text" class="ml-5 mr-10 w-10 h-10"></i>
                    <div>
                        <p class="text-lg font-semibold">Recent Posts</p>
                        <p class="text-2xl">{{ $stats->recent_posts ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6">
                <h3 class="text-xl font-bold">Quick Actions</h3>
                <div class="flex gap-4 mt-2">
                    <a href="{{ route('admin.members') }}" class="bg-[#35408e] text-white px-4 py-2 rounded flex items-center hover:bg-[#2b3373]">
                        <i data-lucide="user-check" class="mr-2"></i> Manage Applications
                    </a>
                    <a href="{{ route('admin.events') }}" class="bg-[#35408e] text-white px-4 py-2 rounded flex items-center hover:bg-[#2b3373]">
                        <i data-lucide="plus-circle" class="mr-2"></i> Create an Event
                    </a>
                    <button onclick="openModal('createPostModal')" class="bg-[#35408e] text-white px-4 py-2 rounded flex items-center hover:bg-[#2b3373]">
                        <i data-lucide="megaphone" class="mr-2"></i> Create a Post
                    </button>
                    @if($ongoingEvent)
                        <a href="{{ route('admin.current-event') }}" class="bg-[#35408e] text-white px-4 py-2 rounded flex items-center hover:bg-[#2b3373]">
                            <i data-lucide="calendar-sync" class="mr-2"></i> Current Event
                        </a>
                    @else
                        <button onclick="openModal('noOngoingEventModal')" class="bg-[#35408e] text-white px-4 py-2 rounded flex items-center hover:bg-[#2b3373]">
                            <i data-lucide="calendar-sync" class="mr-2"></i> Current Event
                        </button>
                    @endif
                </div>
            </div>

            <!-- List of Posts -->
            <div class="mt-6 ">
                <h2 class="text-[24px] font-semibold mb-5">List of Posts</h2>
                <div class="bg-[#ffffff] overflow-x-auto h-[500px] overflow-y-auto border border-gray-300 rounded-md shadow-md">
                    <table class="w-full table-fixed text-center bg-[#ffffff]">
                        <thead class="border">
                            <tr>
                                <th class="p-3 w-[100px]">Post Title</th>
                                <th class="p-3 w-[100px]">Event Name</th>
                                <th class="p-3 w-[100px]">Post Description</th>
                                <th class="p-3 w-[80px]">Created At</th>
                                <th class="p-3 w-[100px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr class="border">
                                    <td class="p-3 w-[100px]">{{ $post->post_title }}</td>
                                    <td class="p-3 w-[100px]">{{ $post->event->event_name }}</td>
                                    <td class="p-3 w-[100px]">{{ Str::limit($post->post_content, 20) }}</td>
                                    <td class="p-3 w-[80px]">{{ $post->post_date_time->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                                    <td class="p-3 w-[100px]">
                                        <div class="flex space-x-2 items_center justify-center">
                                            <button onclick="openEditPostModal({{ $post->post_id }})" class="bg-blue-500 text-white px-3 py-2 rounded flex items-center justify-center space-x-2">
                                                <i data-lucide="edit" class="w-5 h-5">Edit</i>
                                                <span>Edit</span>
                                            </button>
                                            <button onclick="openDeletePostModal({{ $post->post_id }})" class="bg-red-500 text-white px-3 py-2 rounded flex items-center justify-center space-x-2">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                <span>Remove</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create a New Post Modal -->
    <div id="createPostModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-[800px]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[24px] font-semibold">Create a New Post</h2>
                <button onclick="closeModal('createPostModal')" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="postForm" action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="col-span-1">
                    <input type="text" name="post_title" placeholder="Post Title" required class="p-2 border rounded w-full">
                </div>
                <select name="event_id" required class="p-2 border rounded w-full">
                    <option value="" disabled selected>Select an event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                    @endforeach
                </select>
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    <textarea name="post_content" placeholder="Write post content..." required class="p-2 border rounded w-full h-48"></textarea>
                    <div class="relative w-full h-48 rounded-lg overflow-hidden">
                        <label for="media_file" class="w-full h-full bg-gray-300 border border-gray-400 rounded-lg flex items-center justify-center cursor-pointer text-gray-600 overflow-hidden">
                            <span id="file-label" class="text-center">No file chosen. Click to upload.</span>
                            <img id="preview" class="absolute top-0 left-0 w-full h-full object-contain hidden" />
                        </label>
                        <input type="file" name="media_file" id="media_file" accept="image/*" class="hidden">
                    </div>
                </div>
                <button type="submit" class="bg-black text-white p-2 rounded w-full col-span-2 hover:bg-gray-800 transition">
                    Submit Post
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div id="editPostModal" class="hidden fixed inset-0 z-200 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-[800px]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[24px] font-semibold">Edit Post</h2>
                <button onclick="closeModal('editPostModal')" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form id="editPostForm" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
                @csrf
                @method('PUT') <!-- Ensure this is included for Laravel to handle PUT requests -->
                <input type="text" name="post_title" id="edit_post_title" placeholder="Post Title" required class="p-2 border rounded w-full">
                <select name="event_id" id="edit_event_id" required class="p-2 border rounded w-full">
                    <option value="" disabled selected>Select an event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                    @endforeach
                </select>
                <textarea name="post_content" id="edit_post_content" placeholder="Write post content..." required class="p-2 border rounded w-full h-48"></textarea>
                <div class="relative w-full h-48 rounded-lg overflow-hidden">
                    <label for="edit_media_file" class="w-full h-full bg-gray-300 border border-gray-400 rounded-lg flex items-center justify-center cursor-pointer text-gray-600 overflow-hidden">
                        <span id="edit_file-label" class="text-center">No file chosen. Click to upload.</span>
                        <img id="edit_preview" class="absolute top-0 left-0 w-full h-full object-contain hidden" />
                    </label>
                    <input type="file" name="media_file" id="edit_media_file" accept="image/*" class="hidden">
                </div>
                <button type="submit" class="bg-black text-white p-2 rounded w-full col-span-2 hover:bg-gray-800 transition">
                    Update Post
                </button>
            </form>
        </div>
    </div>

    <!-- Delete Post Confirmation Modal -->
    <div id="deletePostModal" class="hidden fixed inset-0 z-100 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <p class="text-lg font-semibold text-red-500">Are you sure you want to delete this post?</p>
            <div class="mt-4">
                <button onclick="confirmDeletePost()" class="px-4 py-2 bg-red-500 text-white rounded">Yes, Delete</button>
                <button onclick="closeModal('deletePostModal')" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
            </div>
        </div>
    </div>

    <!-- No Ongoing Event Modal -->
    <div id="noOngoingEventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto"></i>
            <p class="text-lg font-semibold mt-4">There is no ongoing event at the moment.</p>
            <button onclick="closeModal('noOngoingEventModal')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Close
            </button>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto"></i>
            <p id="successMessage" class="text-lg font-semibold mt-4"></p>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="hidden fixed inset-0 z-100 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto"></i>
            <p id="errorMessage" class="text-lg font-semibold mt-4"></p>
            <button onclick="closeModal('errorModal')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Confirm
            </button>
        </div>
    </div>

    <script>
        // Function to open a modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        // Function to close a modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Function to show success modal
        function showSuccessModal(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }

        // Function to show error modal
        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        // Handle form submission for creating a post
        const postForm = document.getElementById('postForm');
        if (postForm) {
            postForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                    });

                    const data = await response.json();

                    if (response.ok) {
                        closeModal('createPostModal');
                        showSuccessModal('Post created successfully!');
                    } else {
                        showErrorModal(data.message || 'Failed to create post. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorModal('An unexpected error occurred. Please try again.');
                }
            });
        }

        // Handle media file preview for Create Post Modal
        const mediaFileInput = document.getElementById('media_file');
        if (mediaFileInput) {
            mediaFileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const label = document.getElementById('file-label');
                const preview = document.getElementById('preview');

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        label.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                } else {
                    alert("Please select an image file.");
                }
            });
        }

        const editMediaFileInput = document.getElementById('edit_media_file');
        if (editMediaFileInput) {
            editMediaFileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const label = document.getElementById('edit_file-label');
                const preview = document.getElementById('edit_preview');

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        label.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                } else {
                    alert("Please select an image file.");
                }
            });
        }

        function openEditPostModal(postId) {
            fetch(`/posts/${postId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('edit_post_title').value = data.post_title;
                    document.getElementById('edit_event_id').value = data.event_id;
                    document.getElementById('edit_post_content').value = data.post_content;
                    document.getElementById('editPostForm').action = `/posts/${postId}`;

                    if (data.media && data.media.file_url) {
                        const preview = document.getElementById('edit_preview');
                        preview.src = data.media.file_url;
                        preview.classList.remove('hidden');
                        document.getElementById('edit_file-label').classList.add('hidden');
                    }

                    openModal('editPostModal');
                })
                .catch(error => {
                    console.error('Error fetching post data:', error);
                    showErrorModal('Failed to fetch post data. Please try again.');
                });
        }

        // Handle form submission for editing a post
        const editPostForm = document.getElementById('editPostForm');
if (editPostForm) {
    editPostForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        const postId = this.action.split('/').pop();

        try {
            const response = await fetch(`/posts/${postId}`, {
                method: 'POST', // Use POST for updates (Laravel handles PUT via method spoofing)
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest', // Indicate this is an AJAX request
                },
            });

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                if (response.ok) {
                    closeModal('editPostModal');
                    showSuccessModal('Post updated successfully!');
                } else {
                    showErrorModal(data.message || 'Failed to update post.');
                }
            } else {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                showErrorModal('An unexpected error occurred. Please check the console for details.');
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('An unexpected error occurred.');
        }
    });
}

        // Function to open the Delete Post Confirmation Modal
        let postIdToDelete = null;

        function openDeletePostModal(postId) {
            postIdToDelete = postId;
            openModal('deletePostModal');
        }

        // Function to confirm post deletion
        function confirmDeletePost() {
            if (postIdToDelete) {
                fetch(`/posts/${postIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('deletePostModal');
                        showSuccessModal('Post deleted successfully!');
                    } else {
                        showErrorModal(data.message || 'Failed to delete post.');
                    }
                })
                .catch(error => {
                    console.error('Error deleting post:', error);
                    showErrorModal('An error occurred while deleting the post.');
                });
            }
        }

        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>