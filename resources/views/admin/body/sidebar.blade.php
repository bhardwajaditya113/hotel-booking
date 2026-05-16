@php
    $activeTeam = request()->is('all/team*') || request()->is('add/team*') || request()->is('edit/team*');
    $activeRoomType = request()->routeIs('room.type.list', 'add.room.type') || request()->is('room/type*');
    $activeVerification = request()->is('admin/verification/*');
    $activeBooking = request()->is('booking/list*') || request()->is('edit_booking*');
    $activeRoomList = request()->is('view/room/list*') || request()->is('add/room/list*');
    $activeSettings = request()->is('smtp/setting*') || request()->is('site/setting*');
    $activeTestimonial = request()->is('*testimonial*');
    $activeBlog = request()->is('blog/category*') || request()->is('*blog/post*');
    $activeComment = request()->is('all/comment*');
    $activeReport = request()->is('booking/report*');
    $activeGallery = request()->is('all/gallery*') || request()->is('add/gallery*') || request()->is('edit/gallery*');
    $activeContact = request()->is('contact/message*');
    $activeReviews = request()->is('admin/reviews*');
    $activeCoupons = request()->is('admin/coupons*');
    $activePricing = request()->is('admin/pricing*');
    $activeLoyalty = request()->is('admin/loyalty/*');
    $activePolicies = request()->is('admin/policies*');
    $activeRoles = request()->is('all/permission*') || request()->is('add/permission*') || request()->is('edit/permission*')
        || request()->is('all/roles*') || request()->is('add/roles*') || request()->is('add/roles/permission*')
        || request()->is('all/roles/permission*') || request()->is('admin/edit/roles*');
    $activeAdmins = request()->is('all/admin*') || request()->is('add/admin*') || request()->is('edit/admin*');
@endphp
<aside class="navbar navbar-vertical navbar-expand-lg navbar-light border-end nx-admin-sidebar" aria-label="Admin navigation">
    <div class="container-fluid flex-column align-items-stretch p-0 gap-0 h-100">
        <div class="nx-admin-brand-bar d-flex align-items-center justify-content-between gap-2 w-100 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand m-0 p-0 text-decoration-none d-flex align-items-center min-w-0">
                <x-admin-brand-lockup size="md" class="text-start" />
            </a>
            <button class="navbar-toggler d-lg-none p-2 rounded-3 border-0 nx-admin-sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse flex-grow-1 overflow-y-auto" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-2 pb-3 gap-0">
                <li class="nav-item px-2">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <span class="nav-link-icon"><i class="bx bx-home-alt"></i></span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                @if (Auth::user()->can('team.all') || Auth::user()->can('team.add'))
                    <li class="nav-item px-2 mt-2">
                        <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Team</span>
                    </li>
                    <li class="nav-item px-2">
                        <a class="nav-link {{ $activeTeam ? 'active' : '' }}" href="#nx-nav-team" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeTeam ? 'true' : 'false' }}">
                            <span class="nav-link-icon"><i class="bx bx-group"></i></span>
                            <span class="nav-link-title">Manage teams</span>
                            <span class="nav-link-toggle"></span>
                        </a>
                        <div class="collapse {{ $activeTeam ? 'show' : '' }}" id="nx-nav-team">
                            <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                                @if (Auth::user()->can('team.all'))
                                    <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.team') }}">All team</a></li>
                                @endif
                                @if (Auth::user()->can('team.add'))
                                    <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.team') }}">Add team</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Book area</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ request()->routeIs('book.area') ? 'active' : '' }}" href="{{ route('book.area') }}">
                        <span class="nav-link-icon"><i class="bx bx-book-content"></i></span>
                        <span class="nav-link-title">Update book area</span>
                    </a>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Rooms</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeRoomType ? 'active' : '' }}" href="#nx-nav-roomtype" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeRoomType ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-layer"></i></span>
                        <span class="nav-link-title">Room types</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeRoomType ? 'show' : '' }}" id="nx-nav-roomtype">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('room.type.list') }}">Room type list</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.room.type') }}">Add room type</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Property management</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeVerification ? 'active' : '' }}" href="#nx-nav-properties" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeVerification ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-building-house"></i></span>
                        <span class="nav-link-title">Properties</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeVerification ? 'show' : '' }}" id="nx-nav-properties">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.verification.properties.index') }}">Property verification</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.verification.hosts.index') }}">Host verification</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Bookings</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeBooking ? 'active' : '' }}" href="#nx-nav-booking" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeBooking ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-calendar-check"></i></span>
                        <span class="nav-link-title">Booking</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeBooking ? 'show' : '' }}" id="nx-nav-booking">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('booking.list') }}">Booking list</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.room.list') }}">Add booking</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeRoomList ? 'active' : '' }}" href="#nx-nav-roomlist" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeRoomList ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-list-ul"></i></span>
                        <span class="nav-link-title">Room list</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeRoomList ? 'show' : '' }}" id="nx-nav-roomlist">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('view.room.list') }}">View room list</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Site</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeSettings ? 'active' : '' }}" href="#nx-nav-settings" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeSettings ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-cog"></i></span>
                        <span class="nav-link-title">Settings</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeSettings ? 'show' : '' }}" id="nx-nav-settings">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('smtp.setting') }}">SMTP</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('site.setting') }}">Site settings</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeTestimonial ? 'active' : '' }}" href="#nx-nav-testimonial" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeTestimonial ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-message-square-detail"></i></span>
                        <span class="nav-link-title">Testimonials</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeTestimonial ? 'show' : '' }}" id="nx-nav-testimonial">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.testimonial') }}">All testimonials</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.testimonial') }}">Add testimonial</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeBlog ? 'active' : '' }}" href="#nx-nav-blog" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeBlog ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-news"></i></span>
                        <span class="nav-link-title">Blog</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeBlog ? 'show' : '' }}" id="nx-nav-blog">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('blog.category') }}">Blog categories</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.blog.post') }}">All posts</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeComment ? 'active' : '' }}" href="#nx-nav-comments" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeComment ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-chat"></i></span>
                        <span class="nav-link-title">Comments</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeComment ? 'show' : '' }}" id="nx-nav-comments">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.comment') }}">All comments</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeReport ? 'active' : '' }}" href="#nx-nav-report" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeReport ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-bar-chart-alt-2"></i></span>
                        <span class="nav-link-title">Reports</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeReport ? 'show' : '' }}" id="nx-nav-report">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('booking.report') }}">Booking report</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeGallery ? 'active' : '' }}" href="#nx-nav-gallery" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeGallery ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-images"></i></span>
                        <span class="nav-link-title">Gallery</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeGallery ? 'show' : '' }}" id="nx-nav-gallery">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.gallery') }}">All gallery</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.gallery') }}">Add gallery</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeContact ? 'active' : '' }}" href="#nx-nav-contact" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeContact ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-envelope"></i></span>
                        <span class="nav-link-title">Messages</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeContact ? 'show' : '' }}" id="nx-nav-contact">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('contact.message') }}">Contact messages</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Growth</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeReviews ? 'active' : '' }}" href="#nx-nav-reviews" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeReviews ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-star"></i></span>
                        <span class="nav-link-title">Reviews</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeReviews ? 'show' : '' }}" id="nx-nav-reviews">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.reviews.index') }}">All reviews</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeCoupons ? 'active' : '' }}" href="#nx-nav-coupons" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeCoupons ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-gift"></i></span>
                        <span class="nav-link-title">Coupons</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeCoupons ? 'show' : '' }}" id="nx-nav-coupons">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.coupons.index') }}">All coupons</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.coupons.create') }}">Create coupon</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activePricing ? 'active' : '' }}" href="#nx-nav-pricing" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activePricing ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-dollar-circle"></i></span>
                        <span class="nav-link-title">Pricing rules</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activePricing ? 'show' : '' }}" id="nx-nav-pricing">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.pricing.index') }}">All rules</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.pricing.create') }}">Create rule</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeLoyalty ? 'active' : '' }}" href="#nx-nav-loyalty" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeLoyalty ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-trophy"></i></span>
                        <span class="nav-link-title">Loyalty</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeLoyalty ? 'show' : '' }}" id="nx-nav-loyalty">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.loyalty.rewards.index') }}">All rewards</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.loyalty.rewards.create') }}">Create reward</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activePolicies ? 'active' : '' }}" href="#nx-nav-policies" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activePolicies ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-refresh"></i></span>
                        <span class="nav-link-title">Cancellation</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activePolicies ? 'show' : '' }}" id="nx-nav-policies">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.policies.index') }}">All policies</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('admin.policies.create') }}">Create policy</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">Access</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeRoles ? 'active' : '' }}" href="#nx-nav-roles" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeRoles ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-shield-quarter"></i></span>
                        <span class="nav-link-title">Roles &amp; permissions</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeRoles ? 'show' : '' }}" id="nx-nav-roles">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.permission') }}">All permissions</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.roles') }}">All roles</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.roles.permission') }}">Role in permission</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.roles.permission') }}">All role in permission</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link {{ $activeAdmins ? 'active' : '' }}" href="#nx-nav-admins" data-bs-toggle="collapse" role="button" aria-expanded="{{ $activeAdmins ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="bx bx-user-circle"></i></span>
                        <span class="nav-link-title">Admin users</span>
                        <span class="nav-link-toggle"></span>
                    </a>
                    <div class="collapse {{ $activeAdmins ? 'show' : '' }}" id="nx-nav-admins">
                        <ul class="nav nav-pills flex-column border-start border-2 ms-3 ps-2 my-1">
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('all.admin') }}">All admins</a></li>
                            <li class="nav-item"><a class="nav-link py-1 small" href="{{ route('add.admin') }}">Add admin</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item px-2 mt-2">
                    <span class="nav-link py-1 small text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08em;">External</span>
                </li>
                <li class="nav-item px-2">
                    <a class="nav-link" href="{{ url('/') }}" target="_blank" rel="noopener">
                        <span class="nav-link-icon"><i class="bx bx-globe"></i></span>
                        <span class="nav-link-title">View website</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
