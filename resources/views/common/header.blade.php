<header class="topHeader">
    <section class="wrapper">
        <section class="navSection">
            <section class="logoSection"><img src="{{ asset('public/user/images/logo.png') }}" alt="" /></section>
            <section class="logoRightSection">
                <ul class="location">
                    <li class="phoneLi"><i class="fa fa-phone" aria-hidden="true"></i> 6291154616</li>
                    <li class="addressTop"><i class="fa fa-map-marker" aria-hidden="true"></i> ICMARD Bulding, 6th
                        Floor, 14/2,
                        <br>
                        CIT Scheme- VIII (M), Kolkata - 700067
                    </li>
                    <li class="hallBookingLi"><a href="{{route('index')}}" class="hallBooking"><i class="fa fa-calendar"
                                aria-hidden="true"></i>
                            Accommodation / Hall Booking</a></li>
                    <li class="hallBookingLi"><a href="{{route('Userlogin')}}" class="hallBooking"><i class="fa fa-user"
                                aria-hidden="true"></i>
                             Login</a></li>
                </ul>
            </section>
            <div class="navSecMobile">
                <a class="open-menu btnResponsive" href="#" role="button" onClick="openFun()"><i class="fa fa-bars"
                        aria-hidden="true"></i></a>

                <nav class="sidebar" id="sidebarId">

                    <!-- close sidebar menu -->
                    <div class="dismiss" onClick="closeFun()"><i class="fa fa-angle-right" aria-hidden="true"></i></div>

                    <div class="logoSidebar">
                        <a href="https://www.wbscardb.com/" title="WBSCARDB" rel="home"><img
                                src="https://www.wbscardb.com/wp-content/themes/WBSCARDB-child/assets/images/logo.png"
                                alt=""></a>
                    </div>


                    <ul class="list-unstyled menu-elements">

                        <li><a class="scroll-link" href="https://www.wbscardb.com/"><i class="fa fa-caret-right"
                                    aria-hidden="true"></i>Home </a></li>
                        <li>
                            <a href="#otherSections" data-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle" role="button" aria-controls="otherSections">
                                <i class="fa fa-caret-right" aria-hidden="true"></i>
                                About </a>
                            <ul class="collapse list-unstyled" id="otherSections">
                                <li><a class="scroll-link" href="https://www.wbscardb.com/about-us/the-organization"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>The Organization</a></li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/about-us/objective-mission"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Objective &amp; Mission</a>
                                </li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/about-us/salient-features/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Salient Features</a></li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/about-us/board-of-director"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Board Of Director</a></li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/about-us/from-the-chairman"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>From The Chairman</a></li>
                            </ul>
                        </li>

                        <li><a href="#otherSections2" data-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle" role="button" aria-controls="otherSections">
                                <i class="fa fa-caret-right" aria-hidden="true"></i>Branches Network</a>
                            <ul class="collapse list-unstyled" id="otherSections2">
                                <li><a class="scroll-link" href="https://www.wbscardb.com/branches-network/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Branches Network</a></li>
                                <li><a class="scroll-link"
                                        href="https://www.wbscardb.com/branches-network/list-of-branches/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>List of Branches</a></li>
                                <li><a class="scroll-link"
                                        href="https://www.wbscardb.com/branches-network/lists-of-ardbs-member-banks/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Lists of ARDBs/Member
                                        Banks</a></li>
                                <li><a class="scroll-link"
                                        href="https://www.wbscardb.com/branches-network/training-centre/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Training Centre</a></li>
                            </ul>
                        </li>

                        <li><a href="#otherSections3" data-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle" role="button" aria-controls="otherSections">
                                <i class="fa fa-caret-right" aria-hidden="true"></i>Product &amp; Services</a>
                            <ul class="collapse list-unstyled" id="otherSections3">
                                <li><a class="scroll-link" href="https://www.wbscardb.com/product-services/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Product &amp; Services</a>
                                </li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/deposit"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Deposit</a></li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/loans-advances/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Loans &amp; Advances</a>
                                </li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/refinances/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Refinances</a></li>
                            </ul>
                        </li>
                        <li><a class="scroll-link" href="https://www.wbscardb.com/interest-rate/"><i
                                    class="fa fa-caret-right" aria-hidden="true"></i>Interest Rate</a></li>
                        <li><a class="scroll-link" href="https://www.wbscardb.com/financial-statements/"><i
                                    class="fa fa-caret-right" aria-hidden="true"></i>Financial Statements </a></li>

                        <li>
                            <a href="#otherSections4" data-toggle="collapse" aria-expanded="false"
                                class="dropdown-toggle" role="button" aria-controls="otherSections">
                                <i class="fa fa-caret-right" aria-hidden="true"></i>Notices</a>
                            <ul class="collapse list-unstyled" id="otherSections4">
                                <li><a class="scroll-link" href="https://www.wbscardb.com/notices/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Notices</a></li>
                                <li><a class="scroll-link" href="https://www.wbscardb.com/inter-office-circular/"><i
                                            class="fa fa-caret-right" aria-hidden="true"></i>Inter Office Circular</a>
                                </li>
                            </ul>
                        </li>

                        <li><a class="scroll-link" href="https://www.wbscardb.com/gallery/"><i class="fa fa-caret-right"
                                    aria-hidden="true"></i>Gallery </a></li>
                        <li><a class="scroll-link" href="https://www.wbscardb.com/contact-us/"><i
                                    class="fa fa-caret-right" aria-hidden="true"></i>Contact Us </a></li>
                    </ul>

                    <div class="navSecResponsiv">
                        <ul class="rightNav">
                            <li id="mem_logRes"><a href="#" class="borderBtnSolid"><i class="fa fa-calendar"
                                        aria-hidden="true"></i>
                                    Accommodation / Hall Booking</a></li>
                        </ul>
                    </div>



                </nav>

            </div>
        </section>
        <marquee style="color: blue;font-size: 18px;font-weight: bold;">For cancellation of Booking Please Contact to ICMARD office.Mobile : 6291154616</marquee>
    </section>
</header>