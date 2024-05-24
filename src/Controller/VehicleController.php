<?php

namespace Drupal\vehicle_enquiry\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VehicleController extends ControllerBase {

  protected $httpClient;

  public function __construct($http_client) {
    $this->httpClient = $http_client;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  public function enquireVehicle() {
    $api_key = 'Dm7NwKRQWO4DQHXmYGyEwaB2qaInM9b53YJhrrLv';
    $api_url = 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';

    // Bu örnekte, sabit bir plaka numarası kullanıyoruz. Gerçek bir uygulamada, bunu bir formdan alabilirsiniz.
    $registration_number = 'NA14 GJF';
    $request_body = json_encode(['registrationNumber' => $registration_number]);

    try {
      $response = $this->httpClient->post($api_url, [
        'headers' => [
          'x-api-key' => $api_key,
          'Content-Type' => 'application/json',
        ],
        'body' => $request_body,
      ]);

      if ($response->getStatusCode() == 200) {
        $data = json_decode($response->getBody(), TRUE);
        // Veriyi işle ve sayfada göster
        return [
          '#theme' => 'item_list',
          '#items' => $data,
          '#title' => $this->t('Vehicle Enquiry Data'),
        ];
      }
      else {
        return [
          '#markup' => $this->t('API request failed with status code @code', ['@code' => $response->getStatusCode()]),
        ];
      }
    }
    catch (RequestException $e) {
      watchdog_exception('vehicle_enquiry', $e);
      return [
        '#markup' => $this->t('API request failed. Please check the logs for more details.'),
      ];
    }
  }
}

