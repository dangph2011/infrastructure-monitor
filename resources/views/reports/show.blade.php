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
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row">1</td>
                    <td>{{$report->name}}</td>
                    <td>{{$report->description}}</td>
                    <td>{{$report->created_at}}</td>
                    <td>
                        <a href="{{ route('report.edit', $report->reportid) }}" class="btn btn-info" style="margin-right: 3px;">Edit</a>
                        <button form="delete-report-form-{{$report->reportid}}" type="submit" class="btn btn-danger" style="margin-right: 3px;">Delete</button>
                        <form id="delete-report-form-{{$report->reportid}}" class="delete-report" action="/report/{{$report->reportid}}" method="POST" onsubmit="return confirm('Do you want to delete this report id: {{$report->reportid}}?');">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div style="text-align: center;"><button type="button" id="savePdf" class="btn btn-primary" onclick="generatePDF()">Generate PDF</button></div>
        <div style="text-align: center;"><span id="genmsg" style="display:none; color:#0000FF;"><b>Generating PDF ...</b></span></div>
    </div>

    @for ($i = 0; $i < $reportData->count(); $i++)
        <div id="plotid{{$i}}" class="print-wrap page{{$i}}"></div>
    @endfor



    <script>

        var pdf,page_section,HTML_Width,HTML_Height,top_left_margin;
        var ratio;
        var h1 = 0;
        var w1 = 0;
        var width;
        var height;

        function calculatePDF_height_width(selector,index){
            page_section = $(selector).eq(index);
            HTML_Width = width;
            HTML_Height = height*width/page_section.width()*{{SCALE_RATIO}};
            h1 = (height - HTML_Height)/2 ;
        }

         //Generate PDF
        function generatePDF() {
            $("#savePdf").hide();
		    $("#genmsg").show();
            pdf = new jsPDF('l', 'pt', 'a4');
            width = pdf.internal.pageSize.getWidth();
            height = pdf.internal.pageSize.getHeight();

            var timeStamp = '{{Carbon\Carbon::now()}}';
            const filename  = 'report ' + timeStamp + '.pdf';
            @for ($i = 0; $i < $reportData->count(); $i++)
                @if ($i == 0 && $i == ($reportData->count() - 1))
                    html2canvas($(".print-wrap:eq(0)")[0], { allowTaint: true }).then(function(canvas) {
                        calculatePDF_height_width(".print-wrap",0);
                        var imgData = canvas.toDataURL("image/png", 1.0);
                        pdf.addImage(imgData, 'PNG', w1, h1, HTML_Width, HTML_Height);
                        setTimeout(function() {
                            pdf.save(filename);
                            $("#savePdf").show();
                            $("#genmsg").hide();
                        }, 0);
                    });
                @elseif ($i == 0)
                    html2canvas($(".print-wrap:eq(0)")[0], { allowTaint: true }).then(function(canvas) {
                        calculatePDF_height_width(".print-wrap",0);
                        var imgData = canvas.toDataURL("image/png", 1.0);
                        pdf.addImage(imgData, 'PNG', w1, h1, HTML_Width, HTML_Height);
                    });
                @elseif ($i == ($reportData->count() - 1))
                    html2canvas($(".print-wrap:eq({{$i}})")[0], { allowTaint: true }).then(function(canvas) {
                        calculatePDF_height_width(".print-wrap",{{$i}});
                        var imgData = canvas.toDataURL("image/png", 1.0);
                        pdf.addPage(width, height);
                        pdf.addImage(imgData, 'PNG', w1, h1, HTML_Width, HTML_Height);
                        setTimeout(function() {
                            pdf.save(filename);
                            $("#savePdf").show();
                            $("#genmsg").hide();
                        }, 0);
                    });
                @else
                    html2canvas($(".print-wrap:eq({{$i}})")[0], { allowTaint: true }).then(function(canvas) {
                        calculatePDF_height_width(".print-wrap",{{$i}});
                        var imgData = canvas.toDataURL("image/png", 1.0);
                        pdf.addPage(width, height);
                        pdf.addImage(imgData, 'PNG', w1, h1, HTML_Width, HTML_Height);
                    });
                @endif
            @endfor
        }

        @for ($i = 0; $i < $reportData->count(); $i++)
            var data = {!!$reportData[$i]['data']!!};
            var layout = {!!$reportData[$i]['layout']!!};
            var myDiv = document.getElementById('plotid{{$i}}');
            Plotly.newPlot(myDiv, data, layout);
        @endfor

    </script>

</div>
@endsection
