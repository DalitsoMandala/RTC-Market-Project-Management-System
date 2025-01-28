<div>
    <div wire:ignore class="dropdown d-inline-block ">
        <button @click="$wire.readNotifications()" type="button" class="btn header-item noti-icon"
            id="page-header-notifications-dropdown" data-bs-toggle="offcanvas" href="#notified" role="button"
            aria-controls="offcanvasExample">

            <i class='bx bx-bell fs-3 text-muted'></i>
            @if ($unreadNotifications->count() > 0)
                <span class="noti-dot bg-danger rounded-pill">{{ $unreadNotifications->count() }}</span>
            @endif
        </button>


        <div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="notified"
            aria-labelledby="staticBackdropLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="staticBackdropLabel">
                    Notifications
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div data-simplebar>

                    @foreach ($notifications->take(5) as $notification)
                        @if ($notification->type == 'failed_submissions')
                            <a href="{!! $notification->data['link'] ?? null !!}" class="text-reset notification-item ">
                                <div class="d-flex border-bottom align-items-start ">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-3">
                                            <span
                                                class="avatar-title bg-danger-subtle text-danger rounded-circle font-size-16">
                                                <i class="bx bx-file-blank"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">

                                        <h6 class="mb-1">Submissions Notification! @if (is_null($notification->read_at))
                                                <span class="badge bg-warning">New!</span>
                                            @endif
                                        </h6>

                                        <div class="text-muted">
                                            <p class="mb-1 font-size-13">{{ $notification->data['message'] }}</p>


                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                    class="mdi mdi-clock-outline"></i>
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                        @if ($notification->type == 'submissions')
                            <a href="{!! $notification->data['link'] ?? null !!}" class="text-reset notification-item ">
                                <div class="d-flex border-bottom align-items-start ">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-3">
                                            <span
                                                class="avatar-title bg-success-subtle text-success rounded-circle font-size-16">
                                                <i class="bx bx-file-blank"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Submission Notification! @if (is_null($notification->read_at))
                                                <span class="badge bg-warning">New!</span>
                                            @endif
                                        </h6>
                                        <div class="text-muted">
                                            <p class="mb-1 font-size-13">{{ $notification->data['message'] }}</p>


                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                    class="mdi mdi-clock-outline"></i>
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif

                        @if ($notification->type == 'imports')
                            <a href="{!! $notification->data['link'] ?? null !!}" class="text-reset notification-item ">
                                <div class="d-flex border-bottom align-items-start ">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-3">
                                            <span
                                                class="avatar-title bg-success-subtle text-success rounded-circle font-size-16">
                                                <i class="bx bx-import"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Import Successful! @if (is_null($notification->read_at))
                                                <span class="badge bg-warning">New!</span>
                                            @endif
                                        </h6>
                                        <div class="text-muted">
                                            <p class="mb-1 font-size-13">{{ $notification->data['message'] }}</p>


                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                    class="mdi mdi-clock-outline"></i>
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif

                        @if ($notification->type == 'failed_imports')
                            <a href="{!! $notification->data['link'] ?? null !!}" class="text-reset notification-item ">
                                <div class="d-flex border-bottom align-items-start ">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-3">
                                            <span
                                                class="avatar-title bg-danger-subtle text-danger rounded-circle font-size-16">
                                                <i class="bx bx-import"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Failed to Import your file! @if (is_null($notification->read_at))
                                                <span class="badge bg-warning">New!</span>
                                            @endif
                                        </h6>
                                        <div class="text-muted">
                                            <p class="mb-1 font-size-13">Your file failed to import. Please try again.
                                            </p>


                                            <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                    class="mdi mdi-clock-outline"></i>
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                    @if ($notifications->count() == 0)
                        <div class="py-4 text-center">
                            <p class="mb-0"> <i class="bx bx-bell-off"></i> No notifications available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
