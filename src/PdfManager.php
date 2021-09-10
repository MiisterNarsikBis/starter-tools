<?php

namespace PwStarterKit\Tools;

use Dompdf\Dompdf;
use Dompdf\Options;


/**
 * Class PdfManager
 */
class PdfManager
{
    /**
     * @return Dompdf
     */
    protected function setPdf()
    {
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setDpi(100);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');

        return $dompdf;
    }

    /**
     * Generate pdf for bdc and devis => change $fileName if want to use this service for other cases
     *
     * @param array $params
     * @param string $template
     * @param string $destination
     * @return string
     */
    public function generatePdf(array $params = [], string $template , string $destination )
    {
        $dompdf = $this->setPdf();
        ob_start();
        require($template);
        $html = ob_get_contents();
        ob_get_clean();
        $dompdf->loadHtml($html);

        $dompdf->render();
        $output = $dompdf->output();

        $fileName = $params["client"].'-'.uniqid().'.pdf';
        $filePath = $destination.$fileName;
        $dompdf->stream($output, ["Attachment" => false]); // to use in dev env
        //file_put_contents($filePath, $output);

        return $filePath;
    }

}