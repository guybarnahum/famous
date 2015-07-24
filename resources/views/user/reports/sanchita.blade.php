<section>
    <style>

    .bargraph {
        list-style: none;
        padding-top: 20px;
        }
    ul.bargraph li {
        height: 35px;
        text-align: left;
        line-height: 35px;
        padding: 0px 20px;
        margin-bottom: 5px;
    }

    ul.bargraph li.reddeep {
        background: #ED1C24;
    }
     
    ul.bargraph li.redpink {
        background: #EF465B;
    }
     
    ul.bargraph li.pink {
        background: #E55A6B;
    }

    ul.bargraph li.gray {
        background: #EEEEEE;
    }

    ul.bargraph li.orangered{
        background: #E28159;
    }
     
    ul.bargraph li.orange {
        background: #F99C1C;
    }
     
    ul.bargraph li.yellow {
        background: #F4D41E;
    }
     
    ul.bargraph li.green {
        background: #97B546;
    }
     
    ul.bargraph li.greenbright {
        background: #36B669;
    }
     
    ul.bargraph li.greenblue {
        background: #42BDA5;
    }
     
    ul.bargraph li.blue {
        background: #00AEEF;
    }

    ul.bargraph li.xaxis {
    //    background: url(../images/x-axis.jpg);
        height: 49px;
    }
    </style>

    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <h2>Sanchita Candidate Report</h2>

                    @foreach( $entries as $group => $traits )

                    <h2> {{ $group }} </h2>
                    <ul class="bargraph">

                        @foreach( $traits as $trait )

                        <li class="{{ $trait->class }}"
                            style="{{ $trait->style }}">
                            {{ $trait->name }}:{{ $trait->value }}%
                        </li>

                        @endforeach
                        <li class="xaxis"></li>
                    </ul>
                    <p>

                    @endforeach
            </div>
        </div>
    </div>
</section>