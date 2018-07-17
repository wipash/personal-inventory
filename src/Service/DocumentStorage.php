<?php

namespace App\Service;

use App\Entity\InventoryItem;

class DocumentStorage
{
    /** MongoDB\Client */
    protected $client;

    /** @var boolean Have we initialized the databases and collections? */
    protected $init = false;

    public function __consruct()
    {
        $this->client = new MongoDB\Client($_ENV['DATABASE_URL']);
    }

    /**
     * Get a reference to our inventory collection
     * 
     * @return MongoDB\Collection
     */
    public function getInventory()
    {
        if (!$this->client) {
            throw new \RuntimeException('Error establishing connection to MongoDB');
        }
        if (!$this->init) {
            // Create db if it doesn't exist
            $found = false;
            foreach ($this->client->listDatabases() as $db) {
                if ($db->getName() === 'inventory') {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->client->createDatabase('inventory');
            }

            $found = false;
            foreach ($this->client->inventory->listCollections as $collection) {
                if ($collection->getName() === 'inventory') {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->client->inventory->createCollection('inventory');
            }

            $this->init = true;
        }

        // Return collection
        return $this->client->inventory->inventory;
    }

    /**
     * Get an inventory item
     * 
     * @return App\Entity\InventoryItem
     */
    public function getInventoryItem(string $id) : InventoryItem
    {
        $inventory = $this->getInventory();
        return $inventory->findOne(['_id' => MongoDB\BSON\ObjectId("$id")]);
    }

    /**
     * Persist an inventory item
     * 
     * @return string The ID of the item
     */
    public function saveInventoryItem(InventoryItem $item)
    {
        $inventory = $this->getInventory();
        $result = $inventory->updateOne(
            ['_id' => $item->getId()],
            $item,
            ['upsert' => true]
        );
        return (string) $result->getUpsertedId();
    }

}