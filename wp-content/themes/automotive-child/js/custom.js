jQuery( document ).ready(function() {
	//Toggle sidebar left
	jQuery('.toggle').click(function(){
		jQuery('.sidebar_left').toggleClass("sidebar-collapsed");
		if (jQuery('.sidebar_left').hasClass("sidebar-collapsed")) {
 			jQuery('.toggle').addClass("toggle-collapsed");
 			jQuery('.page-content').removeClass("col-lg-9");
 			jQuery('.page-content').addClass("col-full-width");
    	}else {
    		jQuery('.page-content').addClass("col-lg-9");
    		jQuery('.toggle').removeClass("toggle-collapsed");
    		jQuery('.page-content').removeClass("col-full-width");
    	}
	});
	//Add datepicker in form
	jQuery('.datepick').each(function(){
    	jQuery(this).datepicker();
	});
  //Delete posts button
  jQuery('#confirm-delete').on('show.bs.modal', function(e) {
      jQuery(this).find('.btn-ok').attr('href', jQuery(e.relatedTarget).data('href'));
  });
});

jQuery(document).ready(function($) {
  
  $('.wordpress-ajax-form').on('submit', function(e) {
    e.preventDefault();
    d3.selectAll("#chart > *").remove();
    d3.selectAll("#barchart > *").remove();
    var $form = $(this);
    $.post($form.attr('action'), $form.serialize(), function(data) {
    //bar charts
    var jdata = [
          { "date":"2013-01", "value":53 },
          { "date":"2013-02", "value":165 },
          { "date":"2013-03", "value":269 },
          { "date":"2013-04", "value":344 },
          { "date":"2013-05", "value":376 },
          { "date":"2013-06", "value":410 },
          { "date":"2013-07", "value":421 },
          { "date":"2013-08", "value":405 },
          { "date":"2013-09", "value":376 },
          { "date":"2013-10", "value":359 },
          { "date":"2013-11", "value":392 },
          { "date":"2013-12", "value":433 },
          { "date":"2014-01", "value":455 },
          { "date":"2014-02", "value":478 }
        ];
    var margin = {top: 20, right: 20, bottom: 70, left: 40},
    width = 600 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

    // Parse the date / time
    var parseDate = d3.timeFormat("%Y-%m").parse;

    var x = d3.scaleBand()
    .rangeRound([0, width])
    .padding(0.5);

    var y = d3.scaleLinear().range([height, 0]);

    var xAxis = d3.axisBottom()
        .scale(x)
        .tickFormat(d3.timeFormat("%Y-%m"));

    var yAxis = d3.axisLeft()
        .scale(y)
        .ticks(10);

    var svg = d3.select("#barchart").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", 
              "translate(" + margin.left + "," + margin.top + ")");
        jdata.forEach(function(d) {
            d.date = Date.parse(d.date);
            d.value = +d.value;
        });
      x.domain(jdata.map(function(d) { return d.date; }));
      y.domain([0, d3.max(jdata, function(d) { return d.value; })]);
      svg.append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + height + ")")
          .call(xAxis)
        .selectAll("text")
          .style("text-anchor", "end")
          .attr("dx", "-.8em")
          .attr("dy", "-.55em")
          .attr("transform", "rotate(-90)" );

      svg.append("g")
          .attr("class", "y axis")
          .call(yAxis)
        .append("text")
          .attr("transform", "rotate(-90)")
          .attr("y", 6)
          .attr("dy", ".71em")
          .style("text-anchor", "end")
          .text("Value ($)");

      svg.selectAll("bar")
          .data(jdata)
        .enter().append("rect")
          .style("fill", "steelblue")
          .attr("x", function(d) { return x(d.date); })
          .attr("width", x.bandwidth())
          .attr("y", function(d) { return y(d.value); })
          .attr("height", function(d) { return height - y(d.value); });
      //Get json data to generate reports
      (function(d3) {
        'use strict';
        var dataset = [
          { label: data.name, count: 10 },
          { label: data.email, count: 20 },
          { label: 'Cantaloupe', count: 30 },
          { label: 'India', count: 20 },
          { label: 'Bhutan', count: 20 }
        ];
        var total = d3.sum(dataset, function(d) { 
            return d.count;
        });
        dataset.forEach(function(d) {
            d.percentage = d.count  / total;
        });
        var width = 360;
        var height = 360;
        var radius = Math.min(width, height) / 2;

        var color = d3.scaleOrdinal()
        .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);
        var svg = d3.select('#chart')
          .append('svg')
          .attr('width', width)
          .attr('height', height)
          .append('g')
          .attr('transform', 'translate(' + (width / 2) +
            ',' + (height / 2) + ')');
        var percentageFormat = d3.format(".0%");
        var legendRectSize = 18;
        var legendSpacing = 4;
        var arc = d3.arc()
          .outerRadius(radius - 10)
          .innerRadius(0);

        var pie = d3.pie()
          .value(function(d) { return d.count; })
          .sort(null);

        var path = svg.selectAll('path')
          .data(pie(dataset))
          .enter()
          .append('path')
          .attr('d', arc)
          .attr('fill', function(d, i) {
            return color(d.label);
          });
        var g = svg.selectAll(".arc")
                .data(pie(dataset))
                .enter().append("g")
                .attr("class", "arc");

            g.append("path")
                .attr("d", arc)
                .style("fill", function(d,i) {
                    return color(i);
                });

            g.append("text")
                .attr("transform", function(d) {
                    return "translate(" + arc.centroid(d) + ")";
                })
                .attr("dy", ".35em")
                .style("text-anchor", "middle")
                .text(function(d) {
                    return percentageFormat(d.data.percentage);
                });
        /*var legend = svg.selectAll('.legend')
          .data(color.domain())
          .enter()
          .append('g')
          .attr('class', 'legend')
          .attr('transform', function(d, i) {
            var height = legendRectSize + legendSpacing;
            var offset =  height * color.domain().length / 2;
            var horz = -2 * legendRectSize;
            var vert = i * height - offset;
            return 'translate(' + horz + ',' + vert + ')';
          });

        legend.append('rect')
          .attr('width', legendRectSize)
          .attr('height', legendRectSize)
          .style('fill', color)
          .style('stroke', color);

        legend.append('text')
          .attr('x', legendRectSize + legendSpacing)
          .attr('y', legendRectSize - legendSpacing)
          .text(function(d) { return d; });*/
      })(window.d3);
    }, 'json');
  });
});