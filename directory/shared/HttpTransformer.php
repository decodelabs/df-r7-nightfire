<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpTransformer extends arch\Transformer
{
    public function execute()
    {
        /*
        // See if the url just needs a /
        $url = Legacy::$http->getRequest()->getUrl();

        if($url->path->shouldAddTrailingSlash()) {
            $testUrl = clone $url;
            $testUrl->path->shouldAddTrailingSlash(false);
            $context = clone $this->context;
            $context->location = $context->request = Legacy::$http->getRouter()->urlToRequest($testUrl);

            return (new arch\node\Base($context, function($node) {
                return Legacy::$http->redirect($node->context->request)
                    ->isPermanent(true);
            }));
        }
        */

        $record = $this->data->nightfire->node->load($this->context->request);

        if ($record === null) {
            return null;
        }

        return (new arch\node\Base($this->context, function ($node) use ($record) {
            return $record->createResponse($node->context);
        }))
            ->shouldCheckAccess(true)
            ->setDefaultAccess($record->getNodeDefaultAccess())
            ->setAccessSignifiers(...$record->getNodeAccessSignifiers());
    }

    public function canDeliver()
    {
        return $this->data->nightfire->node->exists($this->context->request);
    }


    public function getSitemapEntries(): iterable
    {
        $nodes = $this->data->nightfire->node->select('slug', 'creationDate', 'lastEditDate')
            ->where('isLive', '=', true)
            ->where('isMappable', '=', true);

        foreach ($nodes as $node) {
            yield new arch\navigation\SitemapEntry(
                $this->uri($node['slug']),
                $node['lastEditDate'] ?? $node['creationDate'],
                'weekly'
            );
        }
    }
}
