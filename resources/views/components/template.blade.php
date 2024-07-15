<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        ::after,
        ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
        }

        li {
            list-style: none;
        }

h1 {
    font-weight: 600;
    font-size: 1.5rem;
}

body {
    font-family: 'Poppins', sans-serif;
}

        .wrapper {
            /* display: flex; */
        }

        /* .main {
            min-height: 100vh;
            width: 100%;
            transition: all 0.35s ease-in-out;
            background-color: #fafbfe;
            position: relative;
            z-index: 1;
        } */

        #sidebar {
            width: 70px;
            min-width: 70px;
            z-index: 1000;
            transition: all .25s ease-in-out;
            background-color: #F3E8F3;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
        }

        #sidebar.expand {
            width: 260px;
            min-width: 260px;
        }

        .profileDetail {
            background-color: #fafbfe;
            border-radius: 10px;
            padding: 5px;
            display: block;
        }

        .toggle-btn {
            background-color: transparent;
            cursor: pointer;
            border: 0;
            padding: 1rem 1.5rem 1rem 1.75rem;
        }

        .toggle-btn i {
            font-size: 1.5rem;
            color: #000000;
        }

        .sidebar-logo {
            margin: auto 0;
        }

        .sidebar-logo a {
            color: #000000;
            font-size: 1.15rem;
            font-weight: 600;
        }

        #sidebar:not(.expand) .sidebar-logo,
        #sidebar:not(.expand) a.sidebar-link span,
        #sidebar:not(.expand) .profileDetail {
            visibility: hidden;
        }

        .sidebar-nav {
            padding: 2rem 0;
            flex: 1 1 auto;
        }

        a.sidebar-link {
            padding: .625rem 1.625rem;
            color: #000000;
            display: block;
            font-size: 0.9rem;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .sidebar-link i {
            font-size: 1.75rem;
            margin-right: .75rem;
        }

        a.sidebar-link:hover {
            background-color: rgba(255, 255, 255, .075);
            border-left: 3px solid #3b7ddd;
        }

        .sidebar-item {
            position: relative;
        }

        #sidebar:not(.expand) .sidebar-item .sidebar-dropdown {
            position: absolute;
            top: 0;
            left: 70px;
            background-color: #0e2238;
            padding: 0;
            min-width: 15rem;
            display: none;
        }

        #sidebar:not(.expand) .sidebar-item:hover .has-dropdown+.sidebar-dropdown {
            display: block;
            max-height: 15em;
            width: 100%;
            opacity: 1;
        }

        #sidebar.expand .sidebar-link[data-bs-toggle="collapse"]::after {
            border: solid;
            border-width: 0 .075rem .075rem 0;
            content: "";
            display: inline-block;
            padding: 2px;
            position: absolute;
            right: 1.5rem;
            top: 1.4rem;
            transform: rotate(-135deg);
            transition: all .2s ease-out;
        }

        #sidebar.expand .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
            transform: rotate(45deg);
            transition: all .2s ease-out;
        }

        .btn-light.btn-sm {
            background-color: #D9D9D9;
            border: 15px;
        }

        .btn.edit {
            margin-left: auto;
        }

.sidebar-footer {
     margin-bottom: 10vh;
}

.menu-logo i{
    font-size: 1.5rem;
}

        .user-img{
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-bottom: 2%;
            /* margin: auto; */
        }

        .menu-logo i {
            font-size: 1.5rem;
        }

    </style>
    @yield('extra-css')
</head>

<body>
    {{-- <div class="wrapper"> --}}
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn menu-logo" type="button">
                    <i class="material-symbols-outlined mr-10">
                        menu
                    </i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">POV.it</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link flex-column d-flex justify-content-center">
                        <div class="d-flex">
                            <!-- ini yang diubah untuk gambar yang dipassing -->
                            {{-- <i class="lni lni-user"></i>  --}}
                            <img src="{{asset('profile_pics/'.$user->profile_pics)}}" class="user-img" id="image_preview">
                            <span>Profile</span>
                        </div>

                        <!-- hidden detail navbar -->
                        <div class="profileDetail w-100">
                            <div class="mb-3 mt-1 ">

                                <form action="{{ route('update.profile') }}" method="POST" enctype="multipart/form-data" id="profileForm" >
                                    @csrf
                                    <input type="file" name="profile_pics" id="profile_pics" class="form-control d-none">
                                    <button type="button" class="btn btn-light btn-sm edit-profile-pic">Edit Picture</button>
                                    <button type="submit" class="d-none" id="hiddenSubmitButton"></button>
                                </form>
                            </div>


                            <div class="mb-3">
                                <span class="form-label" style="font-size: 14px">Username</span>
                                <form action="{{ route('update.username') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="d-flex">
                                        <input type="text" class="form-control" id="username_input" name="username" value="{{auth()->user()->name}}" disabled>
                                        <button type="button" class="btn" id="edit-username-btn">
                                            <i class="material-symbols-outlined">
                                                edit_square
                                            </i>
                                        </button>
                                        <button type="submit" class="d-none" id="submitChangeUsr"></button>
                                    </div>
                                </form>
                            </div>
                            <div class="mb-1">
                                <span class="form-label" style="font-size: 14px">Profile Description</span>
                                <form action="{{ route('update.profile.desc') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="d-flex ">
                                        <input type="text" class="form-control" id="desc_input" name="profile_desc" value="{{ auth()->user()->profile_desc }}" disabled>
                                        <button type="button" class="btn" id="edit-desc-btn">
                                            <i class="material-symbols-outlined">
                                                edit_square
                                            </i>
                                        </button>
                                         <button type="submit" class="d-none" id="submitChangeDesc"></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </a>

                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link d-flex align-items-center">
                        <i class="material-symbols-outlined">
                            home
                        </i>
                        <span>Home</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link d-flex align-items-center">
                        <i class="material-symbols-outlined">
                            chat
                        </i>
                        <span>Chat</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                      <i class="material-symbols-outlined">
                        star
                      </i>
                      <span>Close Friend</span>
                    </a>

                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link d-flex align-items-center">
                        <i class="material-symbols-outlined">
                            photo_library
                        </i>
                        <span>History</span>
                    </a>
                </li>
            </ul>

            @include('main.closefriend')

            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="sidebar-link d-flex align-items-center">
                    <i class="material-symbols-outlined">
                        logout
                    </i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Overlay -->
        <div id="overlay" class="overlay" style="display: none;"></div>
        <div class="row w-100" style="height: 100vh">
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;"></div>
            <div class="col-8 d-flex">
                @yield('dashboard')
            </div>
            <div class="col-4 m-0 h-100 p-4">
                @yield('closeFriend')
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>

    <script>
        $(document).ready(function(){
            // Munculkan toast jika ada session success atau error
            @if(session('success'))
                showToast('success', '{{ session('success') }}');
            @endif
            @if(session('error'))
                showToast('danger', '{{ session('error') }}');
            @endif

            // Fungsi untuk menampilkan toast dengan jenis (success/danger) dan pesan
            function showToast(type, message) {
                var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000" data-autohide="true"></div>');
                toast.addClass('text-bg-' + type);
                toast.append('<div class="toast-body">' + message + '</div>');
                $('.toast-container').append(toast);
                toast.toast('show');
            }

            // Hapus toast dari DOM setelah disembunyikan
            $('.toast').on('hidden.bs.toast', function () {
                $(this).remove();
            });

            // Menghilangkan toast jika di-swipe up (opsional)
            $('.toast').on('swipeup', function () {
                $(this).toast('hide');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const hamBurger = document.querySelector(".toggle-btn");
            const overlay = document.querySelector("#overlay");
            const sidebar = document.querySelector("#sidebar");

            // Fungsi untuk meng-expand atau menutup sidebar
            const toggleSidebar = () => {
                sidebar.classList.toggle("expand");
                overlay.style.display = sidebar.classList.contains("expand") ? "block" : "none";
            };

            // Fungsi untuk menutup sidebar
            const closeSidebar = () => {
                sidebar.classList.remove("expand");
                overlay.style.display = "none";
            };

            // Ketika hamburger di-klik
            hamBurger.addEventListener("click", (e) => {
                e.stopPropagation(); // Mencegah event bubbling
                toggleSidebar();
            });

            // Ketika sidebar di-klik, meng-expand jika tidak expand
            sidebar.addEventListener("click", (e) => {
                if (!sidebar.classList.contains("expand")) {
                    e.stopPropagation(); // Mencegah event bubbling
                    toggleSidebar();
                }
            });

            // Ketika overlay di-klik, menutup sidebar
            overlay.addEventListener("click", (e) => {
                e.stopPropagation(); // Mencegah event bubbling
                closeSidebar();
            });

            // Ketika klik di luar sidebar dan hamburger saat expand, menutup sidebar
            document.addEventListener("click", (e) => {
                if (sidebar.classList.contains("expand") && !sidebar.contains(e.target) && !hamBurger.contains(e.target)) {
                    closeSidebar();
                }
            });
        });
    </script>

    @yield('extra-js')

</body>

</html>
