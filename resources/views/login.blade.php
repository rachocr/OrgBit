<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OrgBit</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    @vite('resources/css/app.css')

    <style>
        body {
            background: 
                linear-gradient(135deg, rgba(53, 64, 142, 0.7), rgba(255, 212, 28, 0.7)), /* Gradient layer */
                url("{{ asset('storage/background/NUBackground.jpg') }}") no-repeat center center fixed, /* Background image */
                rgba(0, 0, 0, 0.9); /* Dark overlay layer */
            background-size: cover;
            background-blend-mode: multiply; /* Blend the layers together */
        }
    </style>
</head>

    <body class="bg-[#35408e] flex justify-center items-center h-screen bg-white-100 font-sans">
    <div class="w-full max-w-[450px] text-center">
        <div>
            <img src="{{ asset('storage/logos/Logo.png') }}" alt="logo" class="w-100 h-100 mx-auto">
        </div>
        <div class="text-center mt-5 break-words-30">
            <h1 class="text-2xl font-semibold text- text-[#fcfcfc]">Welcome to Orgbit - Portal for Campus Organizations</h1>
        </div>
        <form action="{{ route('authenticate') }}" method="post" class="text-left">
            @csrf
            <div class="text-[#fcfcfc] mt-10 mb-2 font-medium">
                <p>Email Address</p>
            </div>
            <div class="border-2 rounded-md bg-[#fcfcfc] w-[450px] p-2 text-black mb-5 border-[#fcfcfc] ">
                <input type="text" name="email" class="bg-[#fcfcfc] text-black font-light text-sm  w-[420px] pl-2 ring-0 focus:ring-0 shadow-none focus:shadow-none focus:outline-none border-none">
            </div>
            <div class="text-[#fcfcfc] mb-2 font-medium">
                <p>Password</p>
            </div>
            <div class="border-2 rounded-md  bg-[#fcfcfc] w-[450px] p-2 text-black mb-5 border-[#fcfcfc] ">
                <input type="password" name="password" class="bg-[#fcfcfc] font-light text-sm w-[420px] pl-2 ring-0 focus:ring-0 shadow-none focus:shadow-none focus:outline-none border-none">
            </div>

            
        <!-- Display errors if they exist -->
        @if ($errors->any())
            <div class="bg-red-500 text-[#fcfcfc] p-2 rounded-md  text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="font-semibold text-center">
            <button type="submit" class="h-max p-2 mt-5 w-[450px] border-2 border-[#fcfcfc] rounded-md text-[#fcfcfc] hover:text-[#35408e] hover:bg-[#fcfcfc]  transition duratin-200">Login</button>
        </div>
        </form>
    </div>
</body>
</html>