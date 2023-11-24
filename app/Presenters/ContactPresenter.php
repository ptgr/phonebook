<?php

namespace App\Presenters;

use App\Entity\Contact;
use App\Enum\PhoneNumberType;
use App\Requests\ContactRequest;
use Nette\Application\Responses\VoidResponse;
use Nette\Http\Response;
use App\Entity\Attribute;
use App\Entity\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;

final class ContactPresenter extends \Nette\Application\UI\Presenter
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function actionIndex()
    {
        $this->allowedMethods = ['GET'];
        $this->checkHttpMethod();

        $contacts = $this->entityManager->getRepository(Contact::class)->getWithRelations(null, $this->getParameters());

        $groupedResult = [];
        foreach ($contacts as $contact) {
            $groupedResult[$contact['c_id']][] = $contact;
        }

        $formattedResult = [];
        foreach ($groupedResult as $contact) {
            $formattedResult[] = $this->formatEntity($contact);
        }

        $this->sendJson($formattedResult);
    }

    public function actionShow(int $id)
    {
        $this->allowedMethods = ['GET'];
        $this->checkHttpMethod();

        $contact = $this->entityManager->getRepository(Contact::class)->getWithRelations($id);
        if (empty($contact)) {
            $this->error('Not found', Response::S404_NOT_FOUND);
        }

        $this->sendJson($this->formatEntity($contact));
    }

    private function formatEntity(array $contact): array
    {
        $firstItem = reset($contact);

        $result = [
            'id' => $firstItem['c_id'],
            'firstName' => $firstItem['c_firstName'],
            'lastName' => $firstItem['c_lastName'],
        ];

        foreach ($contact as $item) {
            $result['phoneNumbers'][$item['phoneNumbers_id']] = [
                'id' => $item['phoneNumbers_id'],
                'type' => $item['phoneNumbers_type'],
                'number' => $item['phoneNumbers_number'],
            ];

            $result['attributes'][$item['attribute_id']] = [
                'id' => $item['attribute_id'],
                'name' => $item['attribute_name'],
                'value' => $item['attribute_value'],
            ];
        }

        $result['phoneNumbers'] = array_values($result['phoneNumbers']);
        $result['attributes'] = array_values($result['attributes']);

        return $result;
    }

    public function actionCreate()
    {
        $this->allowedMethods = ['POST'];
        $this->checkHttpMethod();

        try {

            $contactRequest = new ContactRequest($this->getHttpRequest()->getRawBody(), multiPayload: true, requiredItems: ['firstName', 'lastName', 'phoneNumbers']);
            if (!$contactRequest->isValid()) {
                $this->error(json_encode($contactRequest->getErrors()), Response::S400_BAD_REQUEST);
            }

            $contactRepository = $this->entityManager->getRepository(Contact::class);
            $phoneRepository = $this->entityManager->getRepository(PhoneNumber::class);
            $attributeRepository = $this->entityManager->getRepository(Attribute::class);

            foreach ($contactRequest->getParsedData() as $contactData) {

                $contact = $contactRepository->prepare($contactData['firstName'], $contactData['lastName']);

                foreach ($contactData['phoneNumbers'] as $phoneData) {
                    $phoneRepository->prepare(PhoneNumberType::from($phoneData['type']), $phoneData['number'], $contact);
                }

                foreach ($contactData['attributes'] as $attributeData) {
                    $attributeRepository->prepare($attributeData['name'], $attributeData['value'], $contact);
                }
            }

            $contactRepository->save();
            $phoneRepository->save();
            $attributeRepository->save();

        } catch (\Throwable $th) {
            if ($th->getCode() === 0) {
                \Tracy\Debugger::log(json_encode(['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]));
                $this->error("Unexpected error", Response::S500_INTERNAL_SERVER_ERROR);
            } else {
                $this->error($th->getMessage(), $th->getCode());
            }
        }

        $this->getHttpResponse()->setCode(Response::S201_Created);
        $this->sendResponse(new VoidResponse());
    }

    public function actionUpdate(int $id)
    {
        $this->allowedMethods = ['PUT'];
        $this->checkHttpMethod();

        try {

            $contactRequest = new ContactRequest($this->getHttpRequest()->getRawBody(), requiredItems: ['firstName', 'lastName', 'phoneNumbers']);
            if (!$contactRequest->isValid()) {
                $this->error(json_encode($contactRequest->getErrors()), Response::S400_BAD_REQUEST);
            }

            $contactRepository = $this->entityManager->getRepository(Contact::class);
            $phoneRepository = $this->entityManager->getRepository(PhoneNumber::class);
            $attributeRepository = $this->entityManager->getRepository(Attribute::class);

            $contactData = $contactRequest->getParsedData();

            $contact = $contactRepository->update($id, $contactData['firstName'], $contactData['lastName']);

            $phoneRepository->deleteForContact($contact);
            $attributeRepository->deleteForContact($contact);

            foreach ($contactData['phoneNumbers'] as $phoneData) {
                $phoneRepository->prepare(PhoneNumberType::from($phoneData['type']), $phoneData['number'], $contact);
            }

            foreach ($contactData['attributes'] as $attributeData) {
                $attributeRepository->prepare($attributeData['name'], $attributeData['value'], $contact);
            }

            $phoneRepository->save();
            $attributeRepository->save();

        } catch (\Throwable $th) {
            if ($th->getCode() === 0) {
                \Tracy\Debugger::log(json_encode(['error' => $th->getMessage(), 'stack' => $th->getTraceAsString()]));
                $this->error("Unexpected error", Response::S500_INTERNAL_SERVER_ERROR);
            } else {
                $this->error($th->getMessage(), $th->getCode());
            }
        }

        $this->getHttpResponse()->setCode(Response::S204_NO_CONTENT);
        $this->sendResponse(new VoidResponse());
    }
}
