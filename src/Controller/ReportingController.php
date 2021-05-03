<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\GasReading;
use App\Entity\Person;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReportingController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     *
     * @route("/report", name="report", methods={"POST"})
     * @throws \Exception
     */
    public function reportAction(Request $request)
    {
        if (!$request->request->get("timestamp") || !$request->request->get("value") || !$request->request->get("company")) {
            throw new \Exception("Not all required fields given.");
        }

        $reportedDateTime = new DateTime();
        $reportedDateTime->setTimestamp($request->request->get("timestamp"));
        if (!$this->reportWithinLastHour($reportedDateTime)) {
            throw new \Exception("Time must be within past 24 hours!");
        }

        $manager = $this->getDoctrine()->getManager();

        if (!$company = $manager->getRepository(Company::class)->find($request->request->get("company"))) {
            throw new \Exception("No such company exists!");
        }

        $reading = new GasReading();
        $reading->setCompany($company);
        $reading->setCreated($reportedDateTime);
        $reading->setValue($request->request->get("value"));

        $manager->persist($reading);
        $manager->flush();

        return new Response("success");
    }

    private function reportWithinLastHour($reportedDateTime)
    {
        if ($reportedDateTime < (new DateTime())->modify("-1 hour")) {
            return false;
        }

        return true;
    }
}