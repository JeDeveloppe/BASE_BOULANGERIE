<?php

namespace App\Controller\Admin;

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;
use Amenadiel\JpGraph\Plot\GroupBarPlot;
use Amenadiel\JpGraph\Themes\UniversalTheme;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GraphiqueController extends AbstractController
{
    /**
     * @Route("/admin/graphique/", name="admin_graphique_index")
     */
    public function graphiqueVentes(): Response
    {

        return $this->render('admin/graphique/index.html.twig', [
            'controller_name' => 'GraphiqueController',
        ]);
    }

    /**
     * @Route("/admin/graphique/evolution-ventes/{year}/", name="admin_graphique_ventes")
     */
    public function graphiqueVentesEvolution(int $year, DocumentRepository $documentRepository): void
    {
        //annee en cours
        $year = date('Y');
        //annee derniere
        $lastYear = $year - 1;

        $totauxYear = [];

        for($m=1;$m<=12;$m++){

            $startDate = new \DateTimeImmutable("$year-$m-01 T00:00:00");
            $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

            $result = $documentRepository->ventesParMois($startDate,$endDate);

            array_push($totauxYear, $result);
        }

        $totauxLastYear = [];

        for($m=1;$m<=12;$m++){

            $startDate = new \DateTimeImmutable("$lastYear-$m-01 T00:00:00");
            $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

            $result = $documentRepository->ventesParMois($startDate,$endDate);

            array_push($totauxLastYear, $result);
        }
        
        dd($totauxYear);

        $data1y=$totauxYear;
        $data2y=$totauxLastYear;

        // Create the graph. These two calls are always required
        $graph = new Graph(350,200,'auto');
        $graph->SetScale("textlin");

        $theme_class=new UniversalTheme;
        $graph->SetTheme($theme_class);

       //axe des y
        $graph->yaxis->SetTextTickInterval(1,2);
        $graph->SetBox(false);

        $graph->ygrid->SetFill(false);
        $graph->xaxis->SetTickLabels(array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'));
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false,false);

        // Create the bar plots
        $b1plot = new BarPlot($data1y);
        $b1plot->SetLegend($lastYear);
        $b2plot = new BarPlot($data2y);
        $b2plot->SetLegend($year);

        // Create the grouped bar plot
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
        // ...and add it to the graPH
        $graph->Add($gbplot);


        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#cc1111");

        $b2plot->SetColor("white");
        $b2plot->SetFillColor("#11cccc");

        $graph->title->Set("Ventes par mois");

        // Display the graph
        $graph->Stroke();

    }
}
