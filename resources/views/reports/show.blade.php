@extends('layouts.main')
@section('page-header') Report Page
@endsection

@section('scripts')
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script src="{{asset('js/jspdf.min.js')}}""></script>
    <script src="{{asset('js/html2canvas.js')}}"></script>
@endsection

@section('contents')

<div class="container-fluid">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">1</td>
                    <td>{{$report->name}}</td>
                    <td>{{$report->description}}</td>
                    <td>{{$report->created_at}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @for ($i = 0; $i < $reportData->count(); $i++)
        <div id="plotid{{$i}}" class="container-fluid"></div>
    @endfor

    <div><button type="button" class="btn btn-primary" style="float: right;" onclick="print()">Save</button></div>

    <script>
        @for ($i = 0; $i < $reportData->count(); $i++)
            var data = {!!$reportData[$i]['data']!!};
            var layout = {!!$reportData[$i]['layout']!!};
            var myDiv = document.getElementById('plotid{{$i}}');
            Plotly.newPlot(myDiv, data, layout);
        @endfor

        function print() {
            var timeStamp = '{{Carbon\Carbon::now()}}';
            const filename  = 'report ' + timeStamp + '.pdf';
            let pdf = new jsPDF('l', 'mm', 'a4');
            var width = pdf.internal.pageSize.getWidth();
            var height = pdf.internal.pageSize.getHeight();
            var h1 = 0;
            var w1 = 0;
            var widthImage = width - w1;

            for (var i = 0;i <= 2; i++){
                html2canvas(document.querySelector('#plotid'+i)).then(canvas => {
                        var heightImage = (canvas.height - w1) * width / canvas.width;
                        var h1 = (height - heightImage)/2 ;

                        pdf.addImage(canvas.toDataURL('image/png'), 'PNG', w1, h1, widthImage, heightImage);

                        if (i == 2){
                            pdf.save('sample-file.pdf');
                        } else {
                            pdf.addPage();
                        }
                    });
            }
	    }
    </script>

</div>
@endsection
