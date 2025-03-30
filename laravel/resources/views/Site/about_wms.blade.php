@php use App\Services\CustomSiteTranslator; @endphp
@include('Site.MainBlocks.header')

<!--==================================================-->
<!-- Start techo Main Menu  -->
<!--==================================================-->
@include('Site.MainBlocks.menu')
<!--==================================================-->
<!-- End techo Main Menu  -->
<!--==================================================-->


<!--==================================================-->
<!-- Start techo Breadcumb Area -->
<!--==================================================-->
<div class="breadcumb-area align-items-center d-flex">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcumb-content">
                    <div class="breadcumb-title wow fadeInLeft animated">
                        <h2>{{ CustomSiteTranslator::get('About WMS', $lang) }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('HOME', $lang) }}<i
                                            class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ CustomSiteTranslator::get('About WMS', $lang) }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==================================================-->
<!-- End techo Breadcumb Area -->
<!--==================================================-->


<!--==================================================-->
<!-- start techo blog-details-section -->
<!--==================================================-->
<div class="blog-details-section pt-90 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="single-blog-details-box wow fadeInLeft animated">
                    <div class="blog-details-content">

                        {!! CustomSiteTranslator::get("
                            <p>If you think warehouse automation is expensive, complicated, and just not for you… you’re seriously mistaken!</p>
                            <br>
                            <p>And don’t be scared by the word “automation”! I used to associate it with things like robots, conveyor belts, automated welding, factory floors — and, of course, massive budgets and miles of complex processes.</p>
                            <p>If that’s what comes to your mind too — just throw that image away. It’s nothing like that!</p>
                            <br>
                            <b>In this text, we’ll figure out together:</b>
                            <br><br>
                            <ul style='padding-left: 40px;'>
                                <li style='padding-bottom: 10px;'>How you can automate your warehouse for a price comparable to your pocket expenses.</li>
                                <li style='padding-bottom: 10px;'>What exactly we mean by “automation” in this context.</li>
                                <li style='padding-bottom: 10px;'>And in general, we’ll break down what turns out to be a really simple and practical topic.</li>
                            </ul>
                            <p>The main thing is — just get started.</p>
                            <p>And here we go! :)</p>
                        ", $lang) !!}

                        <br>
                        <div style="text-align: center;"><img src="/img/about_wms.jpg" style="border-radius: 12px; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3);"></div>
                        <br>

                        {!! CustomSiteTranslator::get("
                            <h3>Let’s Break It Down:</h3>
                            <br>
                            <ul style='padding-left: 40px;'>
                                <li style='padding-bottom: 10px;'>Why do you need this?</li>
                                <li style='padding-bottom: 10px;'>What will it take to get started?</li>
                                <li style='padding-bottom: 10px;'>And what kind of costs should you expect?</li>
                            </ul>

                            <h3>Why automate your warehouse?</h3>
                            <br>
                            <p>It doesn’t matter whether you have a 1,000-square-meter warehouse or a small 20-square-meter room — implementing a WMS (Warehouse Management System) can completely transform how you work:</p>
                            <br>
                            <ol>
                                <li style='padding-bottom: 10px;'>
                                    <b>Simplicity and accuracy of all operations</b><br>
                                    Receiving goods, placing them on shelves, picking for orders, packing, shipping — everything becomes transparent, manageable, and most importantly, no longer depends on that one “golden employee” who knows where everything is.<br>
                                    Now even a new hire can easily navigate and get things done without turning the warehouse into chaos.<br><br>
                                    I’ve seen it many times: the whole warehouse grinds to a halt the moment the “one who knows everything” goes to lunch or — heaven forbid — takes a vacation. That’s a risk. That’s a loss.<br>
                                    WMS removes the human factor from critical points.
                                </li>

                                <li style='padding-bottom: 10px;'>
                                    <b>Inventory control</b><br>
                                    The second crucial benefit is knowing what you have in stock — and where exactly it is.<br>
                                    No more Excel sheets, notebooks, or yelling “Where’s that box?!”<br>
                                    The system shows you real-time inventory — for every item, in every location. You’ll always know what you have, where it is, and how much is left.
                                </li>

                                <li style='padding-bottom: 10px;'>
                                    <b>Fewer mistakes</b><br>
                                    Automation helps eliminate the most common warehouse errors: wrong items picked, stock mismatches, mixed-up orders, forgotten packages.<br>
                                    Everything becomes predictable and controlled.
                                </li>

                                <li style='padding-bottom: 10px;'>
                                    <b>Speed</b><br>
                                    Time is money.<br>
                                    An automated warehouse runs faster.<br>
                                    An order gets processed in 3 minutes instead of 30.<br>
                                    Customers are happy. Employees aren’t burning out.<br>
                                    Everything runs like clockwork.
                                </li>
                            </ol>
                        ", $lang) !!}

                        {!! CustomSiteTranslator::get("
                            <h3>What do you need to start with WMS?</h3>
                            <br>
                            <p>So, now you understand why warehouse automation is worth it.</p>
                            <p>Let’s talk about what exactly you need to start automating a small warehouse.</p>
                            <br>
                            <p>Good news — the list is pretty short.</p>
                            <p>You don’t need to buy expensive equipment or hire a team of IT specialists — it’s much simpler than that:</p>
                            <br>

                            <ol>
                                <li style='padding-bottom: 10px;'>
                                    <b>Basic Equipment</b>
                                    <br><br>
                                    <p>To launch a basic WMS system, you’ll need:</p>
                                    <ul style='padding-left: 20px;'>
                                        <li style='padding-bottom: 10px;'>
                                            <b>A regular computer or laptop.</b><br>
                                            This is all you need to access your WMS — most systems run right in your browser.
                                        </li>
                                        <li style='padding-bottom: 10px;'>
                                            <b>Smartphones and affordable barcode scanner (or handheld terminal).</b><br>
                                            Even an ordinary smartphone will do just fine when you’re starting out.
                                        </li>
                                        <li style='padding-bottom: 10px;'>
                                            <b>A barcode scanner.</b><br>
                                            This is really the only thing you’ll definitely need to purchase — because barcodes are the foundation of warehouse automation!
                                        </li>
                                    </ul>
                                </li>

                                <li style='padding-bottom: 10px;'>
                                    <b>A WMS System</b>
                                    <br><br>
                                    <b>WMS</b> (<i>Warehouse Management System</i>) is the software that powers it all — the system where you manage every warehouse process from receiving goods to shipping them out.
                                    <br><br>
                                </li>
                            </ol>

                            <p>And that’s basically it!</p>
                            <br>
                            <p>At the end of the day, all you might need to buy is one (or maybe a couple) of low-cost devices. We’ll provide the WMS software for free — and voilà! You’ve got a warehouse that works on a whole new level.</p>
                        ", $lang) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!--==================================================-->
<!-- End techo blog-details-section -->
<!--==================================================-->


@include('Site.MainBlocks.footer')