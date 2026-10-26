<div>

    <div class="main" x-data="dashboard" @updateChartData.window="updateCharts($event.detail.data)">

        <div class="row" data-aos="flip-up">
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-gradient" style="background-color: #fc931d !important;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <svg width="58" height="58" viewBox="0 -0.5 25 25" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M14.9238 7.281C14.9227 8.5394 13.9018 9.55874 12.6435 9.558C11.3851 9.55726 10.3654 8.53673 10.3658 7.27833C10.3662 6.01994 11.3864 5 12.6448 5C13.2495 5.00027 13.8293 5.24073 14.2567 5.6685C14.6841 6.09627 14.924 6.67631 14.9238 7.281Z"
                                stroke="#ffffff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M14.9968 12.919H10.2968C8.65471 12.9706 7.35028 14.3166 7.35028 15.9595C7.35028 17.6024 8.65471 18.9484 10.2968 19H14.9968C16.6388 18.9484 17.9432 17.6024 17.9432 15.9595C17.9432 14.3166 16.6388 12.9706 14.9968 12.919V12.919Z"
                                stroke="#ffffff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20.6878 9.02403C20.6872 9.98653 19.9066 10.7664 18.9441 10.766C17.9816 10.7657 17.2016 9.9852 17.2018 9.0227C17.202 8.06019 17.9823 7.28003 18.9448 7.28003C19.4072 7.28003 19.8507 7.4638 20.1776 7.7909C20.5045 8.11799 20.688 8.56158 20.6878 9.02403V9.02403Z"
                                stroke="#ffffff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.3338 9.02401C4.3338 9.98664 5.11417 10.767 6.0768 10.767C7.03943 10.767 7.8198 9.98664 7.8198 9.02401C7.8198 8.06137 7.03943 7.28101 6.0768 7.28101C5.11417 7.28101 4.3338 8.06137 4.3338 9.02401V9.02401Z"
                                stroke="#ffffff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M19.4368 12.839C19.0226 12.839 18.6868 13.1748 18.6868 13.589C18.6868 14.0032 19.0226 14.339 19.4368 14.339V12.839ZM20.7438 13.589L20.7593 12.8392C20.7541 12.839 20.749 12.839 20.7438 12.839V13.589ZM20.7438 18.24V18.99C20.749 18.99 20.7541 18.9899 20.7593 18.9898L20.7438 18.24ZM19.4368 17.49C19.0226 17.49 18.6868 17.8258 18.6868 18.24C18.6868 18.6542 19.0226 18.99 19.4368 18.99V17.49ZM5.58477 14.339C5.99899 14.339 6.33477 14.0032 6.33477 13.589C6.33477 13.1748 5.99899 12.839 5.58477 12.839V14.339ZM4.27777 13.589V12.839C4.27259 12.839 4.26741 12.839 4.26222 12.8392L4.27777 13.589ZM4.27777 18.24L4.26222 18.9898C4.26741 18.9899 4.27259 18.99 4.27777 18.99V18.24ZM5.58477 18.99C5.99899 18.99 6.33477 18.6542 6.33477 18.24C6.33477 17.8258 5.99899 17.49 5.58477 17.49V18.99ZM19.4368 14.339H20.7438V12.839H19.4368V14.339ZM20.7282 14.3388C21.5857 14.3566 22.2715 15.0568 22.2715 15.9145H23.7715C23.7715 14.2405 22.4329 12.8739 20.7593 12.8392L20.7282 14.3388ZM22.2715 15.9145C22.2715 16.7722 21.5857 17.4724 20.7282 17.4902L20.7593 18.9898C22.4329 18.9551 23.7715 17.5885 23.7715 15.9145H22.2715ZM20.7438 17.49H19.4368V18.99H20.7438V17.49ZM5.58477 12.839H4.27777V14.339H5.58477V12.839ZM4.26222 12.8392C2.58861 12.8739 1.25 14.2405 1.25 15.9145H2.75C2.75 15.0568 3.43584 14.3566 4.29332 14.3388L4.26222 12.8392ZM1.25 15.9145C1.25 17.5885 2.58861 18.9551 4.26222 18.9898L4.29332 17.4902C3.43584 17.4724 2.75 16.7722 2.75 15.9145H1.25ZM4.27777 18.99H5.58477V17.49H4.27777V18.99Z"
                                fill="#ffffff" stroke-width="1" />
                        </svg>
                        <div class="text-white ">
                            <h2 class="text-white" x-text="data.Total"></h2>
                            <span class="fs-18">Number of actors</span>
                        </div>



                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-gradient" style="background: #D87A24">
                    <div class="card-body d-flex align-items-center gap-3">
                        <svg width="58" height="58" version="1.1" id="Icons"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            viewBox="0 0 32 32" xml:space="preserve">
                            <style type="text/css">
                                .st0 {
                                    fill: none;
                                    stroke: #ffffff;
                                    stroke-width: 1;
                                    stroke-linecap: round;
                                    stroke-linejoin: round;
                                    stroke-miterlimit: 10;

                                }
                            </style>
                            <ellipse transform="matrix(0.7071 -0.7071 0.7071 0.7071 -9.481 15.1109)" class="st0"
                                cx="13.5" cy="19" rx="8.1" ry="11" />
                            <path class="st0" d="M10.4,16.4c-0.6,0-1.2,0.2-1.7,0.6c-0.5,0.5-0.7,1.1-0.6,1.7" />
                            <path class="st0" d="M17.1,15.9c-0.6,0-1.2,0.2-1.7,0.6s-0.7,1.1-0.6,1.7" />
                            <path class="st0" d="M17,22.1c-0.6,0-1.2,0.2-1.7,0.6s-0.7,1.1-0.6,1.7" />
                            <path class="st0"
                                d="M14.2,9.7C15.1,5.1,18,3,21.5,3c4.1,0,7.5,2.9,7.5,9.5c0,5.5-2.3,8.4-5.5,9.3" />
                            <path class="st0" d="M24.7,9.6c-0.4-0.4-0.9-0.7-1.5-0.7c-0.6,0-1.1,0.3-1.5,0.7" />
                        </svg>

                        <div class="text-white me-auto">
                            <h2 class="text-white" x-text="data.Potato"></h2>
                            <span class="fs-18">Potato count</span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-gradient" style="background: #C96B28">
                    <div class="card-body d-flex align-items-center gap-3">
                        <svg width="58" height="58" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                            class="iconify iconify--emojione-monotone" preserveAspectRatio="xMidYMid meet">
                            <path
                                d="M57.77 24.233c-4.217-5.481-9.342-9.155-19.889-14.075c-5.955.296-14.948 9.657-14.948 9.657s5.322-8.225 12.055-11.046C29.46 6.262 23.316 4.317 17.222 4.027c-2.67 1.154-5.021 2.604-7.215 4.741c-.04.04 2.133-3.497 4.769-4.768C4.555 4.18 2 7.359 2 7.359s2.157 2.712 2.756 3.707c1.456 2.418 2.635 4.633 3.754 7.12l-.006-.019c1.732-4.844 8.819-10.654 8.766-10.613c-4.215 3.741-6.866 9.238-7.713 13.077l-.008-.016a119.52 119.52 0 0 1 2.153 5.771l-.008-.031c2.764-7.012 12.522-15.19 12.522-15.19s-8.689 10.081-11.422 18.437l-.001-.003c1.485 4.474 6.21 20.104 6.763 21.388C22.1 56.888 27.885 60 34.653 60c3.664 0 7.614-.911 11.497-2.786c14.168-6.838 20.806-21.038 11.62-32.981M45.281 55.409c-3.505 1.693-7.181 2.588-10.628 2.588c-6.289 0-11.123-2.845-13.261-7.804c-3.633-8.434 1.865-16.721 7.116-22.188c4.857-5.05 11.089-8.064 16.668-8.064c4.42 0 8.228 1.907 11.009 5.515c3.368 4.376 4.537 9.276 3.381 14.169c-1.507 6.378-6.714 12.131-14.285 15.784"
                                fill="#ffffff" />
                        </svg>
                        <div class="text-white me-auto">
                            <h2 class="text-white" x-text="data['Sweet potato']"></h2>
                            <span class="fs-18">Sweet potato count</span>
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-gradient" style="background: #A85A1F">
                    <div class="card-body d-flex align-items-center gap-3">

                        <svg fill="#ffffff" width="58" height="58" version="1.1" viewBox="144 144 512 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <g fill-rule="evenodd">
                                <path
                                    d="m359.16 472.39c6.6289-7.8203 13.762-15.574 21.359-23.172 48.941-48.941 104.32-78.363 143.91-80.973 0.082031-0.003906 0.16797-0.011719 0.25-0.015625 9-0.30469 17.789-2.8516 25.566-7.3984 0.042968-0.023438 0.082031-0.050782 0.12891-0.074219 22.617-12.688 46.789-14.027 46.789-14.027 2.2383-0.12109 4.4258 0.70703 6.0117 2.293 1.5859 1.5859 2.4141 3.7734 2.293 6.0117 0 0-1.3477 24.246-14.457 46.695-0.003906 0.003906-0.003906 0.003906 0 0.011719-4.5859 7.8359-7.1484 16.688-7.4492 25.762-0.003906 0.0625-0.003906 0.11719-0.003906 0.17188-2.1992 39.617-31.617 95.039-80.578 144-7.5977 7.5977-15.352 14.73-23.172 21.359 0 0-3.2461-17.984 0.67969-21.586 3.8203-3.5195 7.6094-7.1523 11.359-10.906 45.621-45.621 73.91-96.754 75.98-133.68 0.41016-11.645 3.707-23.012 9.5859-33.062 6.3555-10.887 9.4727-22.266 10.984-30.02-7.8047 1.4688-19.254 4.5156-30.285 10.691-9.9648 5.8125-21.215 9.0664-32.742 9.4844l-0.14453 0.011718c-36.922 2.543-88 30.809-133.57 76.383-3.75 3.75-7.3867 7.543-10.906 11.359-3.6016 3.9258-21.586 0.67969-21.586 0.67969z" />
                                <path
                                    d="m469.58 482.62c-20.035-20.035-44.734-30.762-66.262-31.262-15.48-0.35938-29.414 4.4766-39.5 14.562-10.086 10.086-14.922 24.023-14.562 39.5 0.5 21.523 11.23 46.227 31.262 66.262 20.035 20.035 44.734 30.762 66.262 31.262 15.48 0.35938 29.414-4.4766 39.5-14.562 10.086-10.086 14.922-24.023 14.562-39.5-0.5-21.523-11.23-46.227-31.262-66.262zm-11.133 11.133c16.832 16.832 26.234 37.406 26.656 55.492 0.25391 10.93-2.8359 20.883-9.957 28.004-7.1172 7.1172-17.074 10.207-28.004 9.957-18.086-0.41797-38.66-9.8242-55.492-26.656-16.832-16.832-26.234-37.406-26.656-55.492-0.25391-10.93 2.8359-20.883 9.957-28.004 7.1172-7.1172 17.074-10.207 28.004-9.957 18.086 0.41797 38.66 9.8242 55.492 26.656z" />
                                <path
                                    d="m421.92 614.48c-18.504-5.3242-36.875-16-52.539-31.664-7.293-7.293-13.508-15.176-18.602-23.391-2.2891-3.6914-7.1484-4.8281-10.84-2.5391-3.6914 2.2891-4.8281 7.1484-2.5391 10.84 5.7109 9.2031 12.672 18.043 20.848 26.219 17.672 17.672 38.441 29.656 59.316 35.66 4.1758 1.1992 8.543-1.2109 9.7422-5.3906 1.1992-4.1758-1.2148-8.543-5.3867-9.7383z" />
                                <path
                                    d="m335.98 538.29c-3.0742 3.0742-8.0586 3.0742-11.133 0-3.0742-3.0742-3.0742-8.0586 0-11.133 3.0742-3.0742 8.0586-3.0742 11.133 0 3.0742 3.0742 3.0742 8.0586 0 11.133" />
                                <path
                                    d="m436.18 438.09-19.48 19.48c-3.0703 3.0703 8.0625 14.203 11.133 11.133l19.48-19.48c3.0703-3.0703 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0625-3.0703-11.133 0z" />
                                <path
                                    d="m491.85 404.7-22.266 22.266c-3.0703 3.0703-3.0742 8.0586 0 11.133s8.0625 3.0703 11.133 0l22.266-22.266c3.0703-3.0703 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0625-3.0703-11.133 0z" />
                                <path
                                    d="m489.06 452.01-30.613 30.613 11.133 11.133 30.613-30.613c3.0742-3.0742 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0586-3.0742-11.133 0z" />
                                <path
                                    d="m536.38 449.23-22.266 22.266c-3.0703 3.0703-3.0742 8.0586 0 11.133s8.0625 3.0703 11.133 0l22.266-22.266c3.0703-3.0703 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0625-3.0703-11.133 0z" />
                                <path
                                    d="m502.98 504.89-19.48 19.48c-3.0703 3.0703 8.0625 14.203 11.133 11.133l19.48-19.48c3.0703-3.0703 3.0742-8.0586 0-11.133s-8.0625-3.0703-11.133 0z" />
                                <path
                                    d="m541.95 399.13-19.48 19.48c-3.0703 3.0703-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0625 3.0703 11.133 0l19.48-19.48c3.0703-3.0703 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0625-3.0703-11.133 0z" />
                                <path
                                    d="m222.5 434.21c-10.809 20.762-11.277 42.672-11.277 42.672-0.042968 2.1484 0.78516 4.2266 2.3047 5.7461 1.5195 1.5195 3.5977 2.3477 5.7461 2.3047 0 0 21.91-0.46875 42.688-11.281 21.559-2.5781 51.422-15.195 80.77-30.926 44.379-23.797 87.652-54.488 99.02-65.855l44.531-44.531c20.941-20.941 37.023-55.992 39.465-87.738 1.7188-22.445-3.3906-43.184-17.203-56.992-13.809-13.812-34.551-18.922-56.988-17.203-31.746 2.4453-66.797 18.527-87.738 39.465l-44.531 44.531c-11.367 11.367-42.059 54.641-65.855 99.02-15.727 29.34-28.348 59.199-30.934 80.785zm14.574 6.1211c0.5-0.92578 0.81641-1.9414 0.91797-2.9883 1.9922-20.184 14.324-48.512 29.316-76.477 22.879-42.672 52.184-84.402 63.113-95.328l44.531-44.531c18.559-18.559 49.676-32.73 77.812-34.895 17.488-1.3477 33.883 1.8633 44.648 12.629 10.766 10.766 13.977 27.16 12.629 44.648-2.1641 28.137-16.336 59.254-34.895 77.812l-44.531 44.531c-10.926 10.926-52.656 40.234-95.328 63.113-27.965 14.996-56.293 27.324-76.477 29.316-1.0469 0.10156-2.0664 0.41797-2.9883 0.91797-10.109 5.5117-20.691 8.0195-27.914 9.168 1.1484-7.2266 3.6562-17.809 9.168-27.914z" />
                                <path
                                    d="m219.09 465.92-22.262 22.266c-3.0703 3.0703-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0625 3.0703 11.133 0l22.266-22.266z" />
                                <path
                                    d="m341.55 321.2-77.93 77.93c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0742 11.133 0l77.93-77.93c3.0742-3.0742 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0586-3.0742-11.133 0z" />
                                <path
                                    d="m352.69 365.73-33.398 33.398c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0703 11.133 0l33.398-33.398c3.0742-3.0742 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0586-3.0703-11.133 0z" />
                                <path
                                    d="m436.18 304.5-33.398 33.398c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0703 11.133 0l33.398-33.398c3.0742-3.0742 3.0742-8.0586 0-11.133-3.0742-3.0742-8.0586-3.0703-11.133 0z" />
                                <path
                                    d="m374.95 243.27-27.832 27.832c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0742 11.133 0l27.832-27.832c3.0742-3.0742 3.0742-8.0586 0-11.133s-8.0586-3.0742-11.133 0z" />
                                <path
                                    d="m514.11 237.7-50.098 50.098c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0742 11.133 0l38.965-38.965z" />
                                <path
                                    d="m430.62 243.27-33.398 33.398c-3.0742 3.0742-3.0742 8.0586 0 11.133s8.0586 3.0742 11.133 0l33.398-33.398c3.0742-3.0742 3.0742-8.0586 0-11.133s-8.0586-3.0742-11.133 0z" />
                                <path
                                    d="m497.41 198.74-38.965 38.965c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0742 11.133 0l38.965-38.965c3.0742-3.0742 3.0742-8.0586 0-11.133s-8.0586-3.0742-11.133 0z" />
                                <path
                                    d="m452.88 176.47-38.965 38.965c-3.0742 3.0742-3.0742 8.0586 0 11.133 3.0742 3.0742 8.0586 3.0742 11.133 0l44.531-44.531-5.5664-5.5664c-3.0742-3.0742-8.0586-3.0742-11.133 0z" />
                            </g>
                        </svg>
                        <div class="text-white me-auto">
                            <h2 class="text-white" x-text='data.Cassava'></h2>
                            <span class="fs-18">Cassava count</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row gx-2 gy-2 dash-card" data-aos="fade-up">

            <!-- Profession Distribution -->
            <div class="col-12 col-xl-6">
                <div class="shadow-sm card h-100">
                    <div class="border-0 card-header ">
                        <h5 class="card-title">Project Progress</h5>
                    </div>
                    <div class="card-body">

                        <div id="progressChart"></div>

                        <div class="mt-1 text-center">
                            <p class="mb-0 text-warning fw-bold">Actors Reached</p>
                            <p class="mb-0 text-muted small"><span class="fw-semibold"
                                    style="color: var(--brown);">{{ $projectData['actual'] }}</span> /
                                {{ $projectData['lop'] }} target</p>


                        </div>

                    </div>
                </div>
            </div>


            <!-- Crop Distribution -->
            <div class="col-12 col-xl-6">
                <div class="shadow-sm card h-100">
                    <div class="border-0 card-header">
                        <h5 class="card-title">Crop Distribution</h5>
                    </div>
                    <div class="card-body">

                        <div id="cropChart" x-show='!hasZeroValues(cropChart)'></div>
                        <x-no-data x-show='hasZeroValues(cropChart)' />
                    </div>
                </div>
            </div>

            <!-- Profession Distribution -->
            <div class="col-12 col-xl-12">
                <div class="shadow-sm card h-100">
                    <div class="border-0 card-header ">
                        <h5 class="card-title">Actor Distribution</h5>
                    </div>
                    <div class="card-body">

                        <div id="professionChart" x-show="!hasZeroValues(professionChart)"></div>
                        <x-no-data x-show="hasZeroValues(professionChart)" />
                    </div>
                </div>
            </div>









        </div>


    </div>
</div>
@script
    <script>
        Alpine.data('dashboard', () => ({
            genderChart: [],
            ageGroupChart: [],
            professionChart: [],
            cropChart: [],
            establishmentChart: [],
            data: $wire.entangle('data'),
            genderChartInstance: null,
            ageGroupChartInstance: null,
            professionChartInstance: null,
            cropChartInstance: null,
            establishmentChartInstance: null,
            progressChartInstance: null,
            progressChart: [],
            projectData: $wire.entangle('projectData'),

            hasZeroValues(array) {
                if (!array.every(item => typeof item === 'number')) {
                    throw new Error('Array contains non-number elements.');
                }
                return array.reduce((a, b) => a + b, 0) === 0;
            },
            setData(data) {



                this.professionChart = [data.Farmers, data.Processors, data.Traders, data.Aggregators, data
                    .Transporters
                ];
                this.cropChart = [data.Cassava, data.Potato, data['Sweet potato']];
                this.establishmentChart = [

                    data['New establishment'],
                    data['Old establishment']
                ];


            },


            init() {

                let data = this.data;
                let projectData = this.projectData;
                let projectCalc = projectData.lop === 0 ? 0 : Math.round((projectData.actual / projectData
                    .lop) * 100);

                this.setData(data);

                this.progressChartInstance = new ApexCharts(document.querySelector('#progressChart'), {
                    chart: {
                        type: 'radialBar',
                        height: 300
                    },
                    colors: [SystemColors[0]],
                    series: [projectCalc], // Your progress percentage value (0 - 100)
                    labels: ['LOP Progress'],
                    stroke: {
                        lineCap: 'round' // Rounds the ends of the progress stroke
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '65%'
                            },
                            track: {
                                background: '#f2f2f2'
                            },
                            dataLabels: {
                                show: true,
                                name: {
                                    offsetY: -10,
                                    fontSize: '14px',
                                    color: '#666'
                                },
                                value: {
                                    fontSize: '28px',
                                    fontWeight: 'bold',
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    }
                });

                this.progressChartInstance.render();

                // Initialize charts and store instances
                this.genderChartInstance = new ApexCharts(document.querySelector('#genderChart'), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: SystemColors,
                    legend: {
                        position: 'bottom'
                    },
                    series: this.genderChart,
                    labels: ['Female', 'Male']
                });


                this.ageGroupChartInstance = new ApexCharts(document.querySelector('#ageGroupChart'), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    },
                    colors: SystemColors,
                    series: this.ageGroupChart,
                    labels: ['Youth (18-35 yrs)', 'Not Youth (35yrs+)']
                });


                this.professionChartInstance = new ApexCharts(document.querySelector('#professionChart'), {
                    chart: {
                        type: 'bar',
                        height: 300,

                    },
                    plotOptions: {
                        bar: {
                            distributed: true,

                        }
                    },
                    dataLabels: {
                        enabled: true,

                        style: {
                            colors: [SystemColors[3]],

                        },

                        background: {
                            enabled: true,


                        },

                        offsetX: 0,
                        dropShadow: {
                            enabled: false
                        }
                    },
                    series: [{
                        name: 'Value',
                        data: this.professionChart
                    }],
                    // BAR = white, LINE = red
                    colors: ['#FFEAD8', '#A85A1F'],

                    // Stroke: outline for bars + line color
                    stroke: {
                        width: [1, 1], // bar border, line width
                        colors: ['#F97316', '#A85A1F'] // orange border, red line
                    },

                    plotOptions: {
                        bar: {
                            columnWidth: '35%',

                        }
                    },
                    xaxis: {
                        categories: ['Farmers', 'Processors', 'Traders', 'Aggregators', 'Transporters',

                        ],

                    },


                });
                this.professionChartInstance.render();

                this.cropChartInstance = new ApexCharts(document.querySelector('#cropChart'), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: SystemColors,
                    series: this.cropChart,
                    labels: ['Cassava', 'Potato', 'Sweet Potato'],
                    legend: {
                        position: 'bottom', // Available options: 'top', 'right', 'bottom', 'left'
                        fontSize: '12px',

                    },
                });
                this.cropChartInstance.render();

                this.establishmentChartInstance = new ApexCharts(document.querySelector(
                    '#establishmentChart'), {
                    chart: {
                        type: 'bar',
                        height: 300,

                    },
                    dataLabels: {
                        enabled: true,

                        style: {
                            colors: [SystemColors[3]]
                        },

                        background: {
                            enabled: true,


                        },

                        offsetX: 0,
                        dropShadow: {
                            enabled: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            distributed: true,

                        }
                    },

                    // BAR = white, LINE = red
                    colors: ['#FFEAD8', '#A85A1F'],

                    // Stroke: outline for bars + line color
                    stroke: {
                        width: [1, 1], // bar border, line width
                        colors: ['#F97316', '#A85A1F'] // orange border, red line
                    },

                    plotOptions: {
                        bar: {
                            columnWidth: '35%',

                        }
                    },
                    series: [{
                        name: 'Value',
                        data: this.establishmentChart
                    }],
                    xaxis: {
                        categories: ['New Establishment', 'Old Establishment'],

                    },

                });
                this.establishmentChartInstance.render();

                // $wire.on('updateChartData', (event) => {
                //     data = event.data;

                //     this.setData(data);


                //     // Re-render charts with updated data


                // })

            },

            updateCharts(data) {
                this.setData(data);
                this.genderChartInstance.updateOptions({
                    series: this.genderChart,
                });

                this.cropChartInstance.updateOptions({
                    series: this.cropChart,
                });

                this.ageGroupChartInstance.updateOptions({
                    series: this.ageGroupChart,
                });

                this.professionChartInstance.updateSeries([{
                    data: this.professionChart,
                }]);

                this.establishmentChartInstance.updateSeries([{
                    data: this.establishmentChart,
                }]);
            },

            getRandomNumber() {
                return Math.floor(Math.random() * 10);
            },

            getPercentage(actual, target) {
                if (target === 0) {
                    return 0; // Avoid division by zero
                }
                return Math.round((actual / target) * 100);
            },


        }));
    </script>
@endscript
