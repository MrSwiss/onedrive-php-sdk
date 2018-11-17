<?php

namespace Test\Unit\Krizalys\Onedrive\Proxy;

use Krizalys\Onedrive\Proxy\DriveItemProxy;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;
use Microsoft\Graph\Http\GraphResponse;
use Microsoft\Graph\Model\DriveItem;
use Microsoft\Graph\Model\ItemReference;

class DriveItemProxyTest extends \PHPUnit_Framework_TestCase
{
    const DRIVE_ITEM_ID = '0123';

    public function testCreateFolderShouldReturnExpectedValue()
    {
        $item      = $this->mockDriveItem(self::DRIVE_ITEM_ID);
        $childItem = $this->mockDriveItem('9999');
        $graph     = $this->mockGraphWithResponse(201, $childItem);
        $sut       = new DriveItemProxy($graph, $item);
        $actual    = $sut->createFolder('Test folder', []);
        $this->assertInstanceOf(DriveItemProxy::class, $actual);
        $this->assertSame($actual->parentReference->id, self::DRIVE_ITEM_ID);
    }

    public function testGetChildrenShouldReturnExpectedValue()
    {
        $childItems = [
            $this->mockDriveItem('0001'),
            $this->mockDriveItem('0002'),
        ];

        $item   = $this->mockDriveItem(self::DRIVE_ITEM_ID);
        $graph  = $this->mockGraphWithCollectionResponse($childItems);
        $sut    = new DriveItemProxy($graph, $item);
        $actual = $sut->getChildren();
        $this->assertInternalType('array', $actual);
        $this->assertCount(2, $actual);

        foreach ($actual as $child) {
            $this->assertInstanceOf(DriveItemProxy::class, $child);
        }

        $this->assertSame('0001', $actual[0]->id);
        $this->assertSame('0002', $actual[1]->id);
    }

    public function testDeleteShouldReturnExpectedValue()
    {
    }

    public function testUploadShouldReturnExpectedValue()
    {
    }

    public function testDownloadShouldReturnExpectedValue()
    {
    }

    public function testRenameShouldReturnExpectedValue()
    {
    }

    public function testMoveShouldReturnExpectedValue()
    {
    }

    public function testCopyShouldReturnExpectedValue()
    {
    }

    private function mockGraphWithResponse($status, $payload)
    {
        $response = $this->createMock(GraphResponse::class);
        $response->method('getStatus')->willReturn((string) $status);
        $response->method('getResponseAsObject')->willReturn($payload);
        $request = $this->createMock(GraphRequest::class);
        $request->method('execute')->willReturn($response);
        $graph = $this->createMock(Graph::class);
        $graph->method('createRequest')->willReturn($request);
        $request->method('attachBody')->willReturnSelf();

        return $graph;
    }

    private function mockGraphWithCollectionResponse($payload)
    {
        $response = $this->createMock(GraphResponse::class);
        $response->method('getStatus')->willReturn('200');
        $response->method('getResponseAsObject')->willReturn($payload);
        $request = $this->createMock(GraphRequest::class);
        $request->method('execute')->willReturn($response);
        $graph = $this->createMock(Graph::class);
        $graph->method('createCollectionRequest')->willReturn($request);

        return $graph;
    }

    private function mockDriveItem($id)
    {
        $parentReference = $this->mockItemReference(self::DRIVE_ITEM_ID);
        $item            = $this->createMock(DriveItem::class);
        $item->method('getId')->willReturn($id);
        $item->method('getParentReference')->willReturn($parentReference);

        return $item;
    }

    private function mockItemReference($id)
    {
        $itemReference = $this->createMock(ItemReference::class);
        $itemReference->method('getId')->willReturn($id);

        return $itemReference;
    }
}
