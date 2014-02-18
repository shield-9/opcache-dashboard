/*
	http://codepen.io/pcostanz/pen/jpiHe
	http://www.openspc2.org/reibun/D3.js/code/graph/pie-chart/1007/index.html
	http://www.openspc2.org/reibun/D3.js/code/graph/pie-chart/1001/index.html
*/

jQuery(document).ready(function($) {
	init();
	display();
	set_text("memory");

	function init() {
		var width = $("#graph").width();
		var height = width;
		var radius = width / 2;
		var colors = ['#B41F1F', '#1FB437', '#ff7f0e'];

		d3.scale.customColors = function() {
			return d3.scale.ordinal().range(colors);
		};

		var color = d3.scale.customColors();

		pie = d3.layout.pie()
				.sort(null);

		arc = d3.svg.arc()
				.innerRadius(radius * 0.60)
				.outerRadius(radius * 0.95);

		g = d3.select("#graph").append("svg")
				.attr("width", width)
				.attr("height", height)
				.append("g")
				.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

		if(!isset_view())
			current_view = 'memory';

		path = g.selectAll("path")
				.data(pie(dataset[current_view]))
				.enter()
				.append("path")
				.attr("fill", function(d, i) { return color(i); });
	}

	function re_init() {
		var width = $("#graph").width();
		var height = width;
		var radius = width / 2;

		arc = d3.svg.arc()
				.innerRadius(radius * 0.60)
				.outerRadius(radius * 0.95);

		g = d3.select("#graph svg")
				.attr("width", width)
				.attr("height", height)
				.select("g")
				.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

		if(!isset_view())
			current_view = 'memory';
	}

	function set_text(t) {
		if(t=="memory") {
			$("#stats").html(
				"<table><tr><th style='background:#B41F1F;'>Used</th><td>"+mem_stats[0]+"</td></tr>"
				+"<tr><th style='background:#1FB437;'>Free</th><td>"+mem_stats[1]+"</td></tr>"
				+"<tr><th style='background:#ff7f0e;' rowspan=\"2\">Wasted</th><td>"+mem_stats[2]+"</td></tr>"
				+"<tr><td>"+mem_stats[3]+"%</td></tr></table>"
			);
		} else if(t=="keys") {
			$("#stats").html(
				"<table><tr><th style='background:#B41F1F;'>Cached keys</th><td>"+dataset[t][0]+"</td></tr>"
				+"<tr><th style='background:#1FB437;'>Free Keys</th><td>"+dataset[t][1]+"</td></tr></table>"
			);
		} else if(t=="hits") {
			$("#stats").html(
				"<table><tr><th style='background:#B41F1F;'>Misses</th><td>"+dataset[t][0]+"</td></tr>"
				+"<tr><th style='background:#1FB437;'>Cache Hits</th><td>"+dataset[t][1]+"</td></tr></table>"
			);
		}
		current_view = t;
	}

	$("input").change(function (){
		path=g.selectAll("path")
			.data(pie(dataset[this.value]));
		display();
		set_text(this.value);
	});

	$( window ).resize(function() {
		re_init();
		display();
	});

	function display(){
		path.transition()
			.duration(1000)
			.attrTween("d", function(d){
				var interpolate = d3.interpolate(
					{ startAngle : 0, endAngle : 0 },
					{ startAngle : d.startAngle, endAngle : d.endAngle }
				);
				return function(t){
					return arc(interpolate(t));
				}
			});
		console.log('Current_view reset to: ['+current_view+']');
	}

	function isset_view() {
		if(typeof(current_view) == 'undefined')
			return false;
		else
			return true;
	}
});
