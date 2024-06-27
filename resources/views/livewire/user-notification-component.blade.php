<div>
    <div wire:ignore class="dropdown d-inline-block ">
        <button @click="$wire.readNotifications()" type="button" class="btn header-item noti-icon"
            id="page-header-notifications-dropdown" data-bs-toggle="offcanvas" href="#notified" role="button"
            aria-controls="offcanvasExample">

            <i class='bx bxs-bell fs-3 text-muted'></i>
            @if ($notifications->count() > 0)
                <span class="noti-dot bg-danger rounded-pill">{{ $notifications->count() }}</span>
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
                    @if ($notifications->count() === 0)
                        <div class="text-center alert alert-light" role="alert">
                            <i class="bx bx-bell-off"></i> No notifications at the moment.
                        </div>
                    @endif
                    @foreach ($notifications as $notification)
                        @if ($notification->type === 'manual_data_added')
                            <a href="{{ $notification->data['link'] }}" class="text-reset notification-item">
                                <div class="d-flex border-bottom align-items-start ">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-3">
                                            <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                <i class="bx bxs-file-plus"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $notification->data['message'] }}</h6>
                                        <div class="text-muted">
                                            <p class="mb-1 font-size-13">Data has been successfully added. The ID is:
                                                <span class="text-primary">{{ $notification->data['batch_no'] }}</span>
                                                <span class="badge text-success bg-success-subtle">Click to view</span>
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
                        @if ($notification->type === 'batch_data_added')
                            @php
                                $currentRoute = request()->route(); // Get the current route

                                $routePrefix = $currentRoute->getPrefix(); // Get the route prefix

                            @endphp

                            <a href="{{ $notification->data['link'] }}" class="text-reset notification-item">
                                <div class="d-flex border-bottom align-items-start ">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm me-3">
                                            <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                <i class="bx bx-upload"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $notification->data['message'] }}</h6>
                                        <div class="text-muted">
                                            <p class="mb-1 font-size-13">Data has been successfully uploaded. The ID is:
                                                <span class="text-primary">{{ $notification->data['batch_no'] }}</span>
                                                <span class="badge text-success bg-success-subtle">Click to view</span>
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

                </div>
            </div>
        </div>

    </div>

</div>
