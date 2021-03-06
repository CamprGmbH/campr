<?php

namespace AppBundle\Tests\Controller\API;

use AppBundle\Entity\Risk;
use Component\TimeUnit\TimeUnitAwareInterface;
use MainBundle\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class RiskControllerTest extends BaseController
{
    /**
     * @dataProvider getDataForEditAction
     *
     * @param array $content
     * @param       $isResponseSuccessful
     * @param       $responseStatusCode
     * @param       $responseContent
     */
    public function testEditAction(
        array $content,
        $isResponseSuccessful,
        $responseStatusCode,
        $responseContent
    ) {
        $user = $this->getUserByUsername('superadmin');
        $token = $user->getApiToken();

        $this->client->request(
            'PATCH',
            '/api/risks/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ],
            json_encode($content)
        );
        $response = $this->client->getResponse();
        $risk = json_decode($response->getContent(), true);
        $responseContent['updatedAt'] = $risk['updatedAt'];
        $responseContent['responsibilityAvatarUrl'] = $risk['responsibilityAvatarUrl'];

        $this->assertEquals($isResponseSuccessful, $response->isSuccessful());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, $risk);
    }

    /**
     * @return array
     */
    public function getDataForEditAction()
    {
        return [
            [
                [
                    'title' => 'title1',
                ],
                true,
                Response::HTTP_ACCEPTED,
                [
                    'project' => 1,
                    'projectName' => 'project1',
                    'riskStrategy' => 5,
                    'riskStrategyName' => 'risk-strategy1',
                    'riskCategory' => 1,
                    'riskCategoryName' => 'risk-category1',
                    'responsibility' => 3,
                    'responsibilityFullName' => 'FirstName3 LastName3',
                    'responsibilityAvatarUrl' => '',
                    'status' => 3,
                    'statusName' => 'risk-status1',
                    'createdBy' => null,
                    'updatedBy' => null,
                    'createdByFullName' => null,
                    'id' => 1,
                    'title' => 'title1',
                    'description' => 'description1',
                    'impact' => 10,
                    'impactIndex' => 3,
                    'probability' => 10,
                    'probabilityIndex' => 0,
                    'cost' => 1,
                    'potentialCost' => 0.1,
                    'delay' => 1,
                    'delayUnit' => TimeUnitAwareInterface::DAYS,
                    'potentialDelay' => 0.1,
                    'potentialDelayHours' => round(0.1 * 24, 2),
                    'priority' => 1,
                    'priorityName' => 'low',
                    'measures' => [],
                    'measuresTotalCost' => 0,
                    'dueDate' => '2017-03-03 00:00:00',
                    'createdAt' => '2017-01-01 12:00:00',
                    'updatedAt' => '',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForFieldsNotBlankOnEditAction
     *
     * @param array $content
     * @param       $isResponseSuccessful
     * @param       $responseStatusCode
     * @param       $responseContent
     */
    public function testFieldsNotBlankOnEditAction(
        array $content,
        $isResponseSuccessful,
        $responseStatusCode,
        $responseContent
    ) {
        $user = $this->getUserByUsername('superadmin');
        $token = $user->getApiToken();

        $this->client->request(
            'PATCH',
            '/api/risks/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ],
            json_encode($content)
        );
        $response = $this->client->getResponse();

        $this->assertEquals($isResponseSuccessful, $response->isClientError());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, json_decode($response->getContent(), true));
    }

    /**
     * @return array
     */
    public function getDataForFieldsNotBlankOnEditAction()
    {
        return [
            [
                [
                    'title' => '',
                    'description' => '',
                    'cost' => '',
                    'delay' => '',
                    'priority' => '',
                ],
                true,
                Response::HTTP_BAD_REQUEST,
                [
                    'messages' => [
                        'title' => ['The title field should not be blank'],
                        'description' => ['The description field should not be blank'],
                        'cost' => ['The cost field should not be blank'],
                        'delay' => ['The delay field should not be blank'],
                        'priority' => ['The priority field should not be blank'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForDeleteAction()
     *
     * @param $isResponseSuccessful
     * @param $responseStatusCode
     */
    public function testDeleteAction(
        $isResponseSuccessful,
        $responseStatusCode
    ) {
        $risk = new Risk();
        $risk->setTitle('risk3');
        $risk->setDescription('description3');
        $risk->setCost(3);
        $risk->setDelay(3);
        $risk->setDelayUnit(TimeUnitAwareInterface::DAYS);
        $risk->setImpact(10);
        $risk->setProbability(10);
        $risk->setPriority(3);

        $this->em->persist($risk);
        $this->em->flush();

        try {
            $user = $this->getUserByUsername('superadmin');
            $token = $user->getApiToken();

            $this->client->request(
                'DELETE',
                sprintf('/api/risks/%d', $risk->getId()),
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
                ],
                ''
            );
            $response = $this->client->getResponse();

            $this->assertEquals($isResponseSuccessful, $response->isSuccessful());
            $this->assertEquals($responseStatusCode, $response->getStatusCode());
        } finally {
            $this->em->remove($risk);
            $this->em->flush();
        }
    }

    /**
     * @return array
     */
    public function getDataForDeleteAction()
    {
        return [
            [
                true,
                Response::HTTP_NO_CONTENT,
            ],
        ];
    }

    /**
     * @dataProvider getDataForGetAction()
     *
     * @param $url
     * @param $isResponseSuccessful
     * @param $responseStatusCode
     * @param $responseContent
     */
    public function testGetAction($url, $isResponseSuccessful, $responseStatusCode, $responseContent)
    {
        $user = $this->getUserByUsername('superadmin');
        $token = $user->getApiToken();

        $this->client->request(
            'GET',
            $url,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ],
            ''
        );
        $response = $this->client->getResponse();
        $risk = json_decode($response->getContent(), true);
        $responseContent['updatedAt'] = $risk['updatedAt'];
        $responseContent['responsibilityAvatarUrl'] = $risk['responsibilityAvatarUrl'];

        $this->assertEquals($isResponseSuccessful, $response->isSuccessful());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, $risk);
    }

    /**
     * @return array
     */
    public function getDataForGetAction()
    {
        return [
            [
                '/api/risks/2',
                true,
                Response::HTTP_OK,
                [
                    'project' => 1,
                    'projectName' => 'project1',
                    'riskStrategy' => 6,
                    'riskStrategyName' => 'risk-strategy2',
                    'riskCategory' => 2,
                    'riskCategoryName' => 'risk-category2',
                    'responsibility' => 3,
                    'responsibilityFullName' => 'FirstName3 LastName3',
                    'status' => 4,
                    'statusName' => 'risk-status2',
                    'createdBy' => null,
                    'updatedBy' => null,
                    'createdByFullName' => null,
                    'id' => 2,
                    'title' => 'title2',
                    'description' => 'description2',
                    'impact' => 20,
                    'impactIndex' => 3,
                    'probability' => 20,
                    'probabilityIndex' => 0,
                    'cost' => 1,
                    'potentialCost' => 0.2,
                    'delay' => 1,
                    'potentialDelay' => 0.2,
                    'potentialDelayHours' => round(0.2 * 24, 2),
                    'delayUnit' => TimeUnitAwareInterface::DAYS,
                    'priority' => 2,
                    'priorityName' => 'medium',
                    'measures' => [],
                    'measuresTotalCost' => 0,
                    'dueDate' => '2017-03-03 00:00:00',
                    'createdAt' => '2017-01-01 12:00:00',
                    'updatedAt' => null,
                ],
            ],
        ];
    }
}
