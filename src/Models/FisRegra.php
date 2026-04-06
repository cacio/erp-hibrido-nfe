<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fis_regras')]
#[ORM\Index(columns: ['tenant_id', 'uf_origem', 'uf_destino', 'ncm_prefixo'], name: 'idx_fisregras_busca')]
class FisRegra
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'string', length: 36)]
    private string $tenantId;

    #[ORM\Column(name: 'uf_origem', type: 'string', length: 2)]
    private string $ufOrigem;

    #[ORM\Column(name: 'uf_destino', type: 'string', length: 2)]
    private string $ufDestino;

    #[ORM\Column(name: 'ncm_prefixo', type: 'string', length: 8, nullable: true)]
    private ?string $ncmPrefixo = null;

    #[ORM\Column(name: 'cfop_padrao', type: 'string', length: 4, nullable: true)]
    private ?string $cfopPadrao = null;

    #[ORM\Column(name: 'cst_icms', type: 'string', length: 3, nullable: true)]
    private ?string $cstIcms = null;

    #[ORM\Column(name: 'aliq_icms', type: 'decimal', precision: 5, scale: 2)]
    private float $aliqIcms = 0.00;

    #[ORM\Column(name: 'red_bc_icms', type: 'decimal', precision: 5, scale: 2)]
    private float $redBcIcms = 0.00;

    #[ORM\Column(name: 'cst_pis_cofins', type: 'string', length: 3, nullable: true)]
    private ?string $cstPisCofins = null;

    #[ORM\Column(name: 'aliq_pis', type: 'decimal', precision: 5, scale: 2)]
    private float $aliqPis = 0.00;

    #[ORM\Column(name: 'aliq_cofins', type: 'decimal', precision: 5, scale: 2)]
    private float $aliqCofins = 0.00;

    #[ORM\Column(name: 'cst_ibs_cbs', type: 'string', length: 3, nullable: true)]
    private ?string $cstIbsCbs = null;

    #[ORM\Column(name: 'aliq_ibs', type: 'decimal', precision: 5, scale: 2)]
    private float $aliqIbs = 0.00;

    #[ORM\Column(name: 'aliq_cbs', type: 'decimal', precision: 5, scale: 2)]
    private float $aliqCbs = 0.00;

    #[ORM\Column(type: 'boolean')]
    private bool $ativo = true;

    public function getId(): string { return $this->id; }
    public function getTenantId(): string { return $this->tenantId; }
    public function getUfOrigem(): string { return $this->ufOrigem; }
    public function getUfDestino(): string { return $this->ufDestino; }
    public function getNcmPrefixo(): ?string { return $this->ncmPrefixo; }
    public function getCfopPadrao(): ?string { return $this->cfopPadrao; }
    public function getCstIcms(): ?string { return $this->cstIcms; }
    public function getAliqIcms(): float { return (float)$this->aliqIcms; }
    public function getRedBcIcms(): float { return (float)$this->redBcIcms; }
    public function getCstPisCofins(): ?string { return $this->cstPisCofins; }
    public function getAliqPis(): float { return (float)$this->aliqPis; }
    public function getAliqCofins(): float { return (float)$this->aliqCofins; }
    public function getCstIbsCbs(): ?string { return $this->cstIbsCbs; }
    public function getAliqIbs(): float { return (float)$this->aliqIbs; }
    public function getAliqCbs(): float { return (float)$this->aliqCbs; }
    public function isAtivo(): bool { return $this->ativo; }
}
