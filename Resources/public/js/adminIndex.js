$(function(){
   monthSessionChart();
   monthViewsChart();
    
});


function monthViewsChart()
{
    var data = JSON.parse($('#pageViews').attr('data-chartData'));
    log(data);
    var labels = [];
    var datas =[];
    for (var i=0;i<data.length;i++)
    {
        labels.push(data[i].label);
        datas.push(data[i].count);
    }
    var hydrateChart =
        {
            labels:labels,
            datasets: [
            {
                label: "Nombre de pages par jour",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                scaleShowLabels : false,
                data: datas
            }]
        }
        ;
    var ctx = $("#pageViews").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var myNewChart = new Chart(ctx).Line(hydrateChart, {
        bezierCurve : true,
        responsive: false
    });
}


function monthSessionChart()
{
    var data = JSON.parse($('#monthSessions').attr('data-chartData'));
    log(data);
    var labels = [];
    var datas =[];
    for (var i=0;i<data.length;i++)
    {
        labels.push(data[i].month);
        datas.push(data[i].count);
    }
    
    var hydrateChart =
        {
            labels:labels,
            datasets: [
            {
                label: "Nombre de sessions par mois",
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: datas
            }]
        }
        ;
    
    var ctx = $("#monthSessions").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var myNewChart = new Chart(ctx).Line(hydrateChart, {
        bezierCurve : true,
        responsive: false
    });
    
    
    
}


