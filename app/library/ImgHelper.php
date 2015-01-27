<?php

/**
 * 图表生成辅助类
 * User: ww
 * Date: 14-6-16
 * Time: 09:31
 */
class ImgHelper
{
    /**
     * utf8 to gb2312
     * @param $value
     * @param bool $is_Arr
     * @return array|string
     */
    public static function Iconv($value, $is_Arr = false)
    {
        if ($is_Arr) {
            foreach ($value as $v) {
                $title[] = iconv("UTF-8", "gb2312", $v);
            }
        } else {
            $title = iconv("UTF-8", "gb2312", $value);
        }

        return $title;
    }

    /**
     * 柱装图
     * @param $uData 数据。 值为Y轴，键为X轴
     * @param $title 标题
     */
    public static function BarChart($uData, $title = "")
    {
        require_once(__DIR__ . "/../library/jpgraph/jpgraph.php");
        require_once(__DIR__ . "/../library/jpgraph/jpgraph_bar.php");
        require_once(__DIR__ . "/../library/jpgraph/jpgraph_line.php");
        require_once(__DIR__ . "/../library/jpgraph/jpgraph_pie.php");

        $datay = array();
        $name = array();
        foreach ($uData as $key => $ud) {
            $datay[] = $ud;
            $name[] = $key;
        }

        //计算图片总宽度
        $n = (count($datay) + 1) * 2;
        $siglewidth = 35; //--单一宽度
        $TotalWidth = $siglewidth * $n;
        if ($TotalWidth < 500) {
            $TotalWidth = 500;
        }


        // Create the graph. These two calls are always required
        $graph = new Graph($TotalWidth, 220, 'auto');
        $graph->SetScale("textlin");


        $graph->SetBox(false);


        $graph->ygrid->SetFill(false);
        $name = self::Iconv($name, true);
        $graph->xaxis->SetTickLabels($name);
        $graph->xaxis->SetFont(FF_SIMSUN, FS_BOLD, 11);
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false, false);

        // Create the bar plots
        $b1plot = new BarPlot($datay);

        // ...and add it to the graPH
        $graph->Add($b1plot);


        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#000000");
        $b1plot->SetWidth($siglewidth);

        $title = self::Iconv($title);

        $graph->title->Set($title);
        $graph->title->SetFont(FF_SIMSUN, FS_BOLD, 11);

        // Display the graph
        $graph->Stroke();
    }
} 