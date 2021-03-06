<?php

namespace AppBundle\Tests\Controller\API;

use AppBundle\Entity\Contract;
use AppBundle\Entity\Project;
use MainBundle\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class ContractControllerTest extends BaseController
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
            '/api/contracts/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ],
            json_encode($content)
        );
        $response = $this->client->getResponse();

        $contract = json_decode($response->getContent(), true);
        $responseContent['updatedAt'] = $contract['updatedAt'];
        $responseContent['createdByAvatarUrl'] = $contract['createdByAvatarUrl'];
        $responseContent['createdByFullName'] = $contract['createdByFullName'];
        $responseContent['risks'] = $contract['risks'];
        $responseContent['opportunities'] = $contract['opportunities'];

        $this->assertEquals($isResponseSuccessful, $response->isSuccessful());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, $contract);
    }

    /**
     * @return array
     */
    public function getDataForEditAction()
    {
        return [
            [
                [
                    'name' => 'contract1',
                ],
                true,
                Response::HTTP_ACCEPTED,
                [
                    'project' => 1,
                    'projectName' => 'project1',
                    'createdBy' => 1,
                    'updatedBy' => null,
                    'createdByFullName' => 'FirstNametestEditAction 1 LastName1',
                    'id' => 1,
                    'name' => 'contract1',
                    'description' => 'contract-description1',
                    'projectStartEvent' => null,
                    'projectObjectives' => [],
                    'projectLimitations' => [],
                    'projectDeliverables' => [],
                    'proposedStartDate' => '2017-01-01',
                    'proposedEndDate' => '2017-05-01',
                    'forecastStartDate' => null,
                    'forecastEndDate' => null,
                    'createdAt' => '2017-01-01 12:00:00',
                    'updatedAt' => null,
                    'frozen' => false,
                    'approvedAt' => null,
                    'createdByAvatarUrl' => '',
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
            '/api/contracts/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ],
            json_encode($content)
        );
        $response = $this->client->getResponse();
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals($isResponseSuccessful, $response->isClientError());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, $actual);
    }

    /**
     * @return array
     */
    public function getDataForFieldsNotBlankOnEditAction()
    {
        return [
            [
                [
                    'name' => '',
                    'project' => '',
                ],
                true,
                Response::HTTP_BAD_REQUEST,
                [
                    'messages' => [
                        'name' => ['The name field should not be blank'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForNameIsUniqueOnEditAction
     *
     * @param array $content
     * @param       $isResponseSuccessful
     * @param       $responseStatusCode
     * @param       $responseContent
     */
    public function testNameIsUniqueOnEditAction(
        array $content,
        $isResponseSuccessful,
        $responseStatusCode,
        $responseContent
    ) {
        $user = $this->getUserByUsername('superadmin');
        $token = $user->getApiToken();

        $this->client->request(
            'PATCH',
            '/api/contracts/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $token),
            ],
            json_encode($content)
        );
        $response = $this->client->getResponse();
        $actual = json_decode($response->getContent(), true);

        $this->assertEquals($isResponseSuccessful, $response->isClientError());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, $actual);
    }

    /**
     * @return array
     */
    public function getDataForNameIsUniqueOnEditAction()
    {
        return [
            [
                [
                    'name' => 'contract2',
                ],
                true,
                Response::HTTP_BAD_REQUEST,
                [
                    'messages' => [
                        'name' => ['That name is taken'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForDeleteAction
     *
     * @param $isResponseSuccessful
     * @param $responseStatusCode
     */
    public function testDeleteAction(
        $isResponseSuccessful,
        $responseStatusCode
    ) {
        $user = $this->getUserByUsername('superadmin');
        $project = $this
            ->em
            ->getRepository(Project::class)
            ->findOneBy(
                [
                    'name' => 'project1',
                ]
            );
        $contract = new Contract();
        $contract->setName('contract-test');
        $contract->setProject($project);
        $contract->setCreatedBy($user);
        $this->em->persist($contract);
        $this->em->flush();

        $token = $user->getApiToken();

        $this->client->request(
            'DELETE',
            sprintf('/api/contracts/%d', $contract->getId()),
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
     * @dataProvider getDataForGetAction
     *
     * @param $url
     * @param $isResponseSuccessful
     * @param $responseStatusCode
     * @param $responseContent
     */
    public function testGetAction(
        $url,
        $isResponseSuccessful,
        $responseStatusCode,
        $responseContent
    ) {
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

        $contract = json_decode($response->getContent(), true);
        $responseContent['updatedAt'] = $contract['updatedAt'];
        $responseContent['createdByAvatarUrl'] = $contract['createdByAvatarUrl'];
        $responseContent['risks'] = $contract['risks'];
        $responseContent['opportunities'] = $contract['opportunities'];

        $this->assertEquals($isResponseSuccessful, $response->isSuccessful());
        $this->assertEquals($responseStatusCode, $response->getStatusCode());
        $this->assertEquals($responseContent, $contract);
    }

    /**
     * @return array
     */
    public function getDataForGetAction()
    {
        return [
            [
                '/api/contracts/2',
                true,
                Response::HTTP_OK,
                [
                    'project' => 1,
                    'projectName' => 'project1',
                    'createdBy' => 1,
                    'updatedBy' => null,
                    'createdByFullName' => 'FirstName1 LastName1',
                    'id' => 2,
                    'name' => 'contract2',
                    'description' => 'contract-description2',
                    'projectStartEvent' => null,
                    'projectObjectives' => [],
                    'projectLimitations' => [],
                    'projectDeliverables' => [],
                    'proposedStartDate' => '2017-05-01',
                    'proposedEndDate' => '2017-08-01',
                    'forecastStartDate' => null,
                    'forecastEndDate' => null,
                    'createdAt' => '2017-01-01 12:00:00',
                    'updatedAt' => null,
                    'frozen' => false,
                    'approvedAt' => null,
                    'createdByAvatarUrl' => '',
                ],
            ],
        ];
    }
}
