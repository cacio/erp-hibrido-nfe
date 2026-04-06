<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\EntityManagerFactory;
use App\Models\Filial;
use App\Services\NfeCertificadoService;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

class FilialNfeController extends Controller
{
    public function edit(string $id)
    {
        Authorize::authorize('filial.nfe.config');

        $em = EntityManagerFactory::create();
        $filial = $em->find(Filial::class, $id);

        if (!$filial) {
            $this->redirect('/admin/filiais');
        }

        $this->render('admin/filiais/nfe_config', [
            'filial' => $filial,
            'config' => $filial->getConfigNfe(),
        ]);
    }

    public function update(string $id)
    {
        Authorize::authorize('filial.nfe.config');

        try {


            $em = EntityManagerFactory::create();
            $filial = $em->find(Filial::class, $id);

            if (!$filial) {
                $this->redirect('/admin/filiais');
            }

            $config = $filial->getConfigNfe();

            // Ambiente
            $config['ambiente'] = $_POST['ambiente'] ?? 'HOMOLOGACAO';

            // Numeração
            $config['numeracao']['nfe'] = [
                'serie'          => (int) ($_POST['serie'] ?? 1),
                'ultimo_numero'  => (int) ($_POST['ultimo_numero'] ?? 0),
            ];

            // Upload certificado
            if (!empty($_FILES['certificado']['tmp_name'])) {
                $dir = defined('BASE_PATH') ? BASE_PATH . '/storage/certificados' : dirname(dirname(__DIR__)) . '/storage/certificados';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $path = "/storage/certificados/filial_{$filial->getId()}.pfx";

                $service = new NfeCertificadoService();

                $info = $service->validar(
                    $dir . $path,
                    $_POST['senha_certificado']
                );

                move_uploaded_file($_FILES['certificado']['tmp_name'], $dir . $path);

                $config['certificado'] = [
                    'tipo'    => 'A1',
                    'arquivo' => $path,
                    'senha'   => $_POST['senha_certificado'] ?? '',
                ];
            }

            $filial->setConfigNfe($config);
            $em->flush();

            $this->flash('success', 'Configuração NFe salva com sucesso');
            $this->redirect("/admin/filiais/{$id}/nfe");
        } catch (\Exception $e) {
            $this->flash('error', 'Erro ao salvar configuração NFe: ' . $e->getMessage());
            $this->redirect("/admin/filiais/{$id}/nfe");
        }
    }

    public function testar(string $id)
    {
        Authorize::authorize('filial.nfe.config');

        $em = EntityManagerFactory::create();
        $filial = $em->find(Filial::class, $id);

        $config = $filial->getConfigNfe();

        $service = new NfeCertificadoService();

        try {
            $dir = defined('BASE_PATH') ? BASE_PATH : dirname(dirname(__DIR__));
            $info = $service->validar(
                $dir . $config['certificado']['arquivo'],
                $config['certificado']['senha']
            );

            $this->flash(
                'success',
                "Certificado válido até {$info['expiracao']}"
            );
        } catch (\RuntimeException $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect("/admin/filiais/{$id}/nfe");
    }
}
