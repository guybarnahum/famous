<section class="section swatch-white-black has-top" id="about">
    <div class="decor-top">
        <svg class="decor" height="100%" preserveaspectratio="none" version="1.1" viewbox="0 0 100 100" width="100%" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 100 L100 0 L100 100" stroke-width="0"></path>
        </svg>
    </div>
    <div class="container">
        <div class="row">
            <ul class="list-unstyled row box-list">
                <li class="col-md-4 os-animation" data-os-animation="fadeInUp" data-os-animation-delay=".0s">
                    <div class="box-round flat-shadow">
                        <div class="box-dummy"></div>
                        <figure class="box-inner">
                            <img class="svg-inject" width=144px
                                src='{{ $user->pri_photo_large or "assets/images/logo.png" }}'
                                alt={{ $user->name }}
                            />
                            <figcaption class="box-caption">
                                <h4>Since</h4><p>{{$user->created_at}}</p>
                            </figcaption>
                        </figure>
                    </div>
                    <h3 class="text-center">
                        <a href="about-me.html">{{$user->name}}</a>
                        @if (isset($user->slogan)&&!empty($user->slogan))
                            <small class="block">
                                {{ $user->slogan or 'famous!'}}
                            </small>
                        @endif
                    </h3>
                    <p class="text-center ">
                        {{ $user->email }} | uid:
                        {{ $user->id }}
                        {{ $user->opt_out? '(opt-out)':'' }}

                    @if (isset($user->bio)&&!empty($user->bio))
                        {{ $user->bio}}
                    @endif
                    </p>
                    <ul class="list-inline text-center social-icons social-simple">

                        @foreach( $accounts as $account )
                        <li>
                            <a href="about-me.html" target="_self">
                                <i class="fa fa-{{$account->provider}}"></i>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</section>
