'use strict';

jQuery(function ($) {
    function map_charts() {

        var pie_data = [];
        var current = 'all';
        var keys = ['yes', 'no', 'abstain'];

        map_data.forEach(function (d) {
            pie_data[d.id] = conv(d.data);
            console.log(d.id);
        });

        var width = $('#summary-chart').innerWidth(),
            height = width,
            radius = Math.min(width, height) / 2;

        var color = ['#009B37', '#F00601', '#0049AA', '#ffffff'];

        function conv(obj) {
            var arr = [];
            keys.forEach(function (key) {
                var o = {
                    name: key,
                    value: obj[key]
                };
                arr.push(o);
            });
            return arr;
        }

        $('[data-label]').each(function (i, itm) {
            $(itm).find('.square').css({ color: color[i] });
        });

        var color = d3.scale.linear().domain([0, 100]).range([d3.rgb('#009B37'), d3.rgb('#FFE100'), d3.rgb('#F00601')]);

        var colorScale = d3.scale.threshold().domain([.4, .5, .6]).range(["#ca0020", "#f4a582", "#92c5de", "#0571b0"]);

        var pie = d3.layout.pie().value(function (d) {

            return d.value;
        }).sort(null);

        var arc = d3.svg.arc().innerRadius(0).outerRadius(radius - 20);

        var svg = d3.select("#summary-chart").append("svg").attr("width", width).attr("height", height).append("g").attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        var g = svg.datum(pie_data[current]).selectAll("g").data(pie).enter().append("g");

        var path = g.append("path")
        // .attr("fill", function(d, i) {  return color[i]; })
        .attr("class", function (d) {
            return 'arc ' + d.data.name;
        }).attr("d", arc).each(function (d) {
            this._current = d;
        });

        g.append("text").attr("transform", function (d) {
            return "translate(" + arc.centroid(d) + ")";
        }).attr("text-anchor", "middle").attr("font-size", "1.5em").text(function (d) {
            return d.data.value;
        });

        function change(id) {

            console.log(id);
            svg.datum(pie_data[id]);

            console.log(id);
            console.log(pie_data[id].map(function (el) {
                return el['name'] + ' ' + el['value'];
            }));
            console.log('----');

            g = g.data(pie);

            g.select('path').transition().duration(750).attrTween("d", arcTween);
            g.select('text').style('opacity', 0).attr("transform", function (d) {
                return "translate(" + arc.centroid(d) + ")";
            }).text(function (d) {
                return d.data.value;
            }).transition().delay(250).style('opacity', function (d) {
                return d.data.value ? 1 : 0;
            });
        }

        function arcTween(a) {
            var i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function (t) {
                return arc(i(t));
            };
        }

        var text = $('#map-text p').text(),
            $list = $('#map-text ol');

        $('.region').on('mouseover mouseout', function (e) {

            var target = $(e.currentTarget);
            var display = void 0,
                val = $(this).attr('id'),
                name = $(this).data('name'),
                $txt = $('#map-text p'),
                custom_text = map_data.filter(function (x) {
                return x['id'] == val;
            }).pop()['text'];

            if (e.type == 'mouseover') {

                if (target.hasClass('p-nodata')) {
                    display = name + '<br>brak danych';
                    $('.summary-chart').css({ opacity: 0 });
                } else {
                    change(val);
                    if (typeof custom_text !== 'undefined') {
                        display = custom_text;
                    } else {
                        display = name;
                    }
                }

                $('.region').css({
                    opacity: 0.3
                });
                target.css({ opacity: 1 });
                console.log(target[0].getBBox());
            } else if (e.type == 'mouseout') {

                change(current);

                display = text;

                $('.region, .summary-chart').css({
                    opacity: 1
                });
            }

            $txt.html('<h3>' + display + '</h3>');
        });
    }

    if (typeof map_data !== 'undefined') {
        $('#map').addClass('ok');

        map_charts();

        $('.sequential').each(function (i, el) {
            var s = $(el),
                v = s.data('seq');
            s.delay(200 * v).fadeTo(300, 1);
        });
    }
});