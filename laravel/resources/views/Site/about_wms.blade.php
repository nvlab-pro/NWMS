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

                        {{ CustomSiteTranslator::get('If you think warehouse automation is expensive, complicated, and just not for you… you’re seriously mistaken!', $lang) }}
                        <br><br>
                        {{ CustomSiteTranslator::get('And don’t be scared by the word “automation”! I used to associate it with things like robots, conveyor belts, automated welding, factory floors — and, of course, massive budgets and miles of complex processes.', $lang) }}
                        <br>
                        {{ CustomSiteTranslator::get('If that’s what comes to your mind too — just throw that image away. It’s nothing like that!', $lang) }}
                        <br>
                        <br>
                        <b>{{ CustomSiteTranslator::get('In this text, we’ll figure out together:', $lang) }}</b><br>
                        <br>
                        <ul style="padding-left: 40px;">
                            <li style="padding-bottom: 10px;">{{ CustomSiteTranslator::get('How you can automate your warehouse for a price comparable to your pocket expenses.', $lang) }}</li>
                            <li style="padding-bottom: 10px;">{{ CustomSiteTranslator::get('What exactly we mean by “automation” in this context.', $lang) }}</li>
                            <li style="padding-bottom: 10px;">{{ CustomSiteTranslator::get('And in general, we’ll break down what turns out to be a really simple and practical topic.', $lang) }}</li>
                        </ul>

                        {{ CustomSiteTranslator::get('The main thing is — just get started.', $lang) }}<br>
                        {{ CustomSiteTranslator::get('And here we go! :)', $lang) }}<br>

                        <br>
                        <div style="text-align: center;"><img src="/img/about_wms.jpg" style="border-radius: 12px; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3);"></div>
                        <br>

                        <h3>{{ CustomSiteTranslator::get('Let’s Break It Down:', $lang) }}</h3>
                        <br>
                        <ul style="padding-left: 40px;">
                            <li style="padding-bottom: 10px;">{{ CustomSiteTranslator::get('Why do you need this?', $lang) }}</li>
                            <li style="padding-bottom: 10px;">{{ CustomSiteTranslator::get('What will it take to get started?', $lang) }}</li>
                            <li style="padding-bottom: 10px;">{{ CustomSiteTranslator::get('And what kind of costs should you expect?', $lang) }}</li>
                        </ul>


                        <h3>{{ CustomSiteTranslator::get('Why automate your warehouse?', $lang) }}</h3>
                        <br>
                        {{ CustomSiteTranslator::get('It doesn’t matter whether you have a 1,000-square-meter warehouse or a small 20-square-meter room — implementing a WMS (Warehouse Management System) can completely transform how you work:', $lang) }}
                        <br><br>
                        <ol>
                            <li style="padding-bottom: 10px;">
                                <b>{{ CustomSiteTranslator::get('Simplicity and accuracy of all operations', $lang) }}</b><br>
                                {{ CustomSiteTranslator::get('Receiving goods, placing them on shelves, picking for orders, packing, shipping — everything becomes transparent, manageable, and most importantly, no longer depends on that one “golden employee” who knows where everything is.', $lang) }}
                                <br>
                                {{ CustomSiteTranslator::get('Now even a new hire can easily navigate and get things done without turning the warehouse into chaos.', $lang) }}
                                <br>
                                <br>
                                {{ CustomSiteTranslator::get('I’ve seen it many times: the whole warehouse grinds to a halt the moment the “one who knows everything” goes to lunch or — heaven forbid — takes a vacation. That’s a risk. That’s a loss.', $lang) }}
                                <br>
                                {{ CustomSiteTranslator::get('WMS removes the human factor from critical points.', $lang) }}
                                <br>
                            </li>

                            <li style="padding-bottom: 10px;">
                                <b>{{ CustomSiteTranslator::get('Inventory control', $lang) }}</b><br>
                                {{ CustomSiteTranslator::get('The second crucial benefit is knowing what you have in stock — and where exactly it is.', $lang) }}
                                <br>
                                {{ CustomSiteTranslator::get('No more Excel sheets, notebooks, or yelling “Where’s that box?!”', $lang) }}
                                <br>
                                {{ CustomSiteTranslator::get('The system shows you real-time inventory — for every item, in every location. You’ll always know what you have, where it is, and how much is left.', $lang) }}
                                <br>
                            </li>

                            <li style="padding-bottom: 10px;">
                                <b>{{ CustomSiteTranslator::get('Fewer mistakes', $lang) }}</b><br>
                                {{ CustomSiteTranslator::get('Automation helps eliminate the most common warehouse errors: wrong items picked, stock mismatches, mixed-up orders, forgotten packages.', $lang) }}
                                <br>
                                {{ CustomSiteTranslator::get('Everything becomes predictable and controlled.', $lang) }}
                                <br>
                            </li>

                            <li style="padding-bottom: 10px;">
                                <b>{{ CustomSiteTranslator::get('Speed', $lang) }}</b><br>
                                {{ CustomSiteTranslator::get('Time is money.', $lang) }}<br>
                                {{ CustomSiteTranslator::get('An automated warehouse runs faster.', $lang) }}
                                {{ CustomSiteTranslator::get('An order gets processed in 3 minutes instead of 30.', $lang) }}
                                {{ CustomSiteTranslator::get('Customers are happy. Employees aren’t burning out.', $lang) }}
                                {{ CustomSiteTranslator::get('Everything runs like clockwork.', $lang) }}
                            </li>
                        </ol>

                        <h3>{{ CustomSiteTranslator::get('What do you need to start with WMS?', $lang) }}</h3>
                        <br>
                        {{ CustomSiteTranslator::get('So, now you understand why warehouse automation is worth it.', $lang) }}<br>
                        {{ CustomSiteTranslator::get('Let’s talk about what exactly you need to start automating a small warehouse.', $lang) }}<br>
                        <br>
                        {{ CustomSiteTranslator::get('Good news — the list is pretty short.', $lang) }}<br>
                        {{ CustomSiteTranslator::get('You don’t need to buy expensive equipment or hire a team of IT specialists — it’s much simpler than that:', $lang) }}<br>
                        <br>
                        <ol>
                            <li style="padding-bottom: 10px;">
                                <b>{{ CustomSiteTranslator::get('Basic Equipment', $lang) }}</b><br>
                                <br>
                                {{ CustomSiteTranslator::get('To launch a basic WMS system, you’ll need:', $lang) }}<br>
                                <br>
                                <ul style="padding-left: 20px;">
                                    <li style="padding-bottom: 10px;">
                                    <b>{{ CustomSiteTranslator::get('A regular computer or laptop.', $lang) }}</b><br>
                                    {{ CustomSiteTranslator::get('This is all you need to access your WMS — most systems run right in your browser.', $lang) }}
                                    </li>

                                    <li style="padding-bottom: 10px;">
                                        <b>{{ CustomSiteTranslator::get('Smartphones or an affordable barcode scanner (or handheld terminal).', $lang) }}</b><br>
                                    {{ CustomSiteTranslator::get('Even an ordinary smartphone will do just fine when you’re starting out.', $lang) }}
                                    </li>

                                    <li style="padding-bottom: 10px;">
                                        <b>{{ CustomSiteTranslator::get('A barcode scanner.', $lang) }}</b><br>
                                    {{ CustomSiteTranslator::get('This is really the only thing you’ll definitely need to purchase — because barcodes are the foundation of warehouse automation!', $lang) }}
                                    </li>
                                </ul>
                            </li>

                            <li style="padding-bottom: 10px;">
                                <b>{{ CustomSiteTranslator::get('A WMS System', $lang) }}</b><br>
                                <br>
                                {!! CustomSiteTranslator::get('<b>WMS</b> (<i>Warehouse Management System</i>) is the software that powers it all — the system where you manage every warehouse process from receiving goods to shipping them out.', $lang) !!}<br>
                                <br>
                            </li>
                        </ol>

                        {{ CustomSiteTranslator::get('And that’s basically it!', $lang) }}<br>
                        <br>
                        {{ CustomSiteTranslator::get('At the end of the day, all you might need to buy is one (or maybe a couple) of low-cost devices. We’ll provide the WMS software for free — and voilà! You’ve got a warehouse that works on a whole new level.', $lang) }}
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