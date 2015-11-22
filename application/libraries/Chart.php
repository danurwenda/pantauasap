<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Chart library to help you generate Highchart javascript codes.
 *
 * @author Administrator
 */
class Chart {

    protected $_ci;
    private $colors = array(
        '#AA4643',
        '#4572A7',
        '#89A54E',
        '#80699B',
        '#3D96AE',
        '#DB843D',
        '#92A8CD',
        '#A47D7C',
        '#B5CA92'
    );

    private $zoomImg;

    public function __construct() {
        $this->_ci = &get_instance();
        $this->_ci->load->model('data_model');
        $this->zoomImg = image_asset_url('zoomhc.png', 'deputi3');
    }

    /**
     * Beda frekuensi beda penyajian tooltip
     * @param type $chart
     * @return type
     */
    function parseTooltipFormatter($series, $forDetail = false) {
        $dateFormat = '';
        $periodHandle = '';
        //assuming semua series pakai frekuensi yang sama
        $frek_id = isset($series[0]) ? $series[0]->frekuensi : 1;
        if ($frek_id == 7) {
            $dateFormat = "Highcharts.dateFormat('%Y', this.x)";
        } else if ($frek_id == 4 || $frek_id == 5) {
            $periodHandle = "
                var month = Highcharts.dateFormat('%m',this.x);
                var nth;
                if(month=='01'){
                    nth = '1';
                }
                else if(month=='04'){
                    nth = '2';
                }
                else if(month=='07'){
                    nth = '3';
                }
                else if(month=='10'){
                    nth = '4';
                }";
            $dateFormat = "'Q'+nth +Highcharts.dateFormat(' %Y', this.x)"; //got problem
        } else
        if ($frek_id == 2) {
            $periodHandle = "
                var day = Highcharts.dateFormat('%e',this.x);
                var nth;
                if(day<8){
                    nth = '1';
                }
                else if(day<15){
                    nth = '2';
                }
                else if(day<22){
                    nth = '3';
                }
                else if(day<29){
                    nth = '4';
                }
                else{
                nth = '5';
                }";
            $dateFormat = "'Minggu ke '+nth +' '+Highcharts.dateFormat('%B %Y', this.x)"; //got problem
        } else
        if ($frek_id == 3 || $frek_id == 8) {
            $dateFormat = "Highcharts.dateFormat('%B %Y', this.x)";
        } else
        if ($frek_id == 1) {
            $dateFormat = "Highcharts.dateFormat('%e %b %Y', this.x)";
        }

        if (count($series) == 1) {
            if ($forDetail) {
                $unit = $series[0]->unit;
                return "function() {" . $periodHandle . "
                                return '<b>'+ this.series.name +'</b><br/>'+"
                        . $dateFormat . "+' : ' + Highcharts.numberFormat(this.y, 2)+ ' $unit';
                            }";
            } else {
                return "function() {" . $periodHandle . "
                                return '<b>'+ this.series.name +'</b><br/>'+"
                        . $dateFormat . "+' : ' + Highcharts.numberFormat(this.y, 2)+ ' ' +this.series.yAxis.axisTitle.textStr;
                            }";
            }
        } else {
            return "function() {" .
                    $periodHandle
                    . "return '<b>'+ this.series.name +'</b><br/>'+" . $dateFormat . "+': '+ Highcharts.numberFormat(this.y, 2)+' '+ this.series.yAxis.axisTitle.textStr;
                }";
        }
    }

    function generate($chart, $params =array() ) {
        $formatter = $this->parseTooltipFormatter($chart->series);
        $renderTo = array_key_exists('render-to', $params) ? $params['render-to'] : 'chart';
        $eventImg = image_asset_url('flaghc', 'deputi3');
        $eventButton = (array_key_exists('event-button', $params) && $params['event-button'] == true) ?
                "eventButton:{
                _titleKey:'eventButtonTitle',onclick:function(){},
                symbol: 'url($eventImg)'
            }," : "";

        $expandButton = (array_key_exists('expand-button', $params) && $params['expand-button'] == true) ? "expandButton:{
                _titleKey:'expandButtonTitle',onclick:expand4Chart,
                symbol: 'url($this->zoomImg)'
            }," : "";
        $customButtons = $eventButton . $expandButton;
        $title = anchor('indikator/' . $chart->title_indikator, $chart->title);

        $ret = "var $renderTo = new Highcharts.Chart({
    chart: {
        reflow:false,
        renderTo: '$renderTo'
    },
    tooltip: {
            formatter: $formatter   
        },
    exporting: {
        buttons: {
            contextButton: {
                enabled: false
            },$customButtons
        }
    },
    title: {
        text: '$title'
    },
    subtitle: {
        text: '$chart->source'
    },
    xAxis: {
        type: 'datetime', labels: {
            style: {
                fontSize: '14px'
            }
        }
    },
    yAxis: [";
        //generate yAxis(es)
        $yAxisIdx = 0;
        $leftYNum = 0;
        $oppositeYNum = 0;
        $yAxisJS = ''; //string representation of js config of yAxis
        foreach ($chart->series as $s) {
            //first loop
            if ($s->opposite_y === 't') {
                $oppositeYNum++;
                //ambil warna
                $color = $this->colors[$yAxisIdx];
                $yAxisIdx++;
                $yAxisJS.=
                        "{labels: {
                                style: {
                                    fontSize: '20px',
                                    color: '$color'
                                }
                            },
                            title: {
                                text: '$s->unit',
                                style: {
                                    color: '$color',fontWeight:'bold'
                                }
                            },showEmpty: false,
                            opposite: true},";
            } else {
                $leftYNum++;
                //ambil warna
                $color = $this->colors[$yAxisIdx];
                $yAxisIdx++;
                $yAxisJS.="{labels: {
                                style: {
                                    fontSize: '20px',
                                    color: '$color'
                                }
                            },showEmpty: false,
                            title: {
                                text: '$s->unit',
                                style: {
                                    color: '$color',fontWeight:'bold'
                                }
                            }},";
            }
        }
        $oneAxis = false;
        if ($oppositeYNum == 0 || $leftYNum == 0) {
            //using one y-Axis only, left positioned
            $yAxisJS = "{labels: {
                                style: {
                                    fontSize: '20px'
                                }
                            },showEmpty: false,
                            title: {
                                text: '$s->unit',
                                style: {
                                    fontWeight:'bold'
                                }
                            }}";

            $oneAxis = true;
        }


        $ret.="$yAxisJS],    
    series: [";
        //reset index
        $yAxisIdx = 0;
        $colorCounter = 0;
        $zIndex = 10;
        foreach ($chart->series as $s) {
            //generate data
            $dataJS = '';
            foreach ($this->_ci->data_model->get_recent_data($s->indikator_id) as $d) {
                //parse date since Date.UTC accepts (2015,0,2) for 2 January
                //instead of (2015,1,2)
                list($y, $m, $da) = explode('-', $d->date);
                $m--;
                $dataJS.="[Date.UTC($y, $m, $da), $d->val],";
            }
            //ambil warna
            $color = $this->colors[$colorCounter];
            $colorCounter++;
            $ret.=
                    "{color: '$color',
                        name: '$s->indikator_name',
                        zIndex: $zIndex,
                        type: '$s->type',
                        data: [ $dataJS]";

            if (!$oneAxis) {
                $ret.=",yAxis: $yAxisIdx";
                $yAxisIdx++;
            }
            $ret.="},";
            $zIndex--;
        }
        $ret.="]});";
        return $ret;
    }

    /**
     * Given sebuah indikator id, return array of configuration needed for master-detail chart mode.
     * Configuration includes
     * -dataJS : array of [Date.UTC(1,2,3),199]
     * -firstDateJS : Date.UTC(1,2,3)
     * -lastDateJS: Date.UTC(1,2,3)
     * -initialDateJS : Date.UTC(1,2,3)
     * -formatterJS : parsetooltipformatter
     * @param type $indikator_id
     */
    public function detail_chart_config($indikator) {
        //get recent data untuk mendapatkan initialDateJS & lastDateJS
        $recent = $this->_ci->data_model->get_recent_data($indikator->indikator_id);

        $initialDateJS = sqlDateToUTC($recent[0]->date);

        $lastDateJS = sqlDateToUTC($recent[count($recent) - 1]->date);

        $formatterJS = $this->parseTooltipFormatter(array($indikator), true);

        $allData = $this->_ci->data_model->get_all_data($indikator->indikator_id, false);

        $firstDateJS = sqlDateToUTC($allData[0]->date);

        $dataJS = '';
        foreach ($allData as $data) {
            $dataJS.="[" . sqlDateToUTC($data->date) . ",$data->val],";
        }
        return array(
            'dataJS' => $dataJS,
            'firstDateJS' => $firstDateJS,
            'lastDateJS' => $lastDateJS,
            'initialDateJS' => $initialDateJS,
            'formatterJS' => $formatterJS
        );
    }

}
