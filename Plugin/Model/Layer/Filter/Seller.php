<?php
namespace Genesisoft\MarketplaceExt\Plugin\Model\Layer\Filter;

class Seller
{
    /**
     * Faz tradução impossível de fazer pelo CSV
     * @return string
     */
    public function afterGetName()
    {
        return 'Loja';
    }

}
