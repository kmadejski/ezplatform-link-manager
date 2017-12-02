<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion as Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause as SortClause;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManagerBundle\Form\Data\URLListData;
use EzSystems\EzPlatformLinkManagerBundle\Form\Mapper\URLMapper;
use EzSystems\EzPlatformLinkManagerBundle\Form\Type\URL\URLEditType;
use EzSystems\EzPlatformLinkManagerBundle\Pagination\Pagerfanta\URLSearchAdapter;
use EzSystems\EzPlatformLinkManagerBundle\Pagination\Pagerfanta\URLUsagesAdapter;
use EzSystems\PlatformUIBundle\Controller\Controller;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LinkManagerController extends Controller
{
    const DEFAULT_MAX_PER_PAGE = 10;

    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService
     */
    private $urlService;

    /**
     * EzPlatformLinkManagerController constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\URLService $urlService
     */
    public function __construct(URLService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * Renders the URLs list.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $data = new URLListData();

        $form = $this->createListForm($data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $urls = new Pagerfanta(new URLSearchAdapter(
            $this->buildListQuery($data),
            $this->urlService
        ));

        $urls->setCurrentPage($data->page);
        $urls->setMaxPerPage($data->limit ? $data->limit : self::DEFAULT_MAX_PER_PAGE);

        return $this->render('EzPlatformLinkManagerBundle:LinkManager:list.html.twig', [
            'form' => $form->createView(),
            'can_edit' => $this->isGranted(new Attribute('url', 'update')),
            'urls' => $urls,
        ]);
    }

    /**
     * Displays the edit form and processes it once submitted.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $urlId ID of URL
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $urlId)
    {
        $url = $this->urlService->loadById($urlId);

        $form = $this->createEditForm($url);
        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->urlService->updateUrl($url, $form->getData());
                $this->notify('url.update.success', [], 'linkmanager');

                return $this->redirectToRouteAfterFormPost('admin_link_manager_list');
            } catch (\Exception $e) {
                $this->notifyError('url.update.error', [], 'linkmanager');
            }
        }

        return $this->render('EzPlatformLinkManagerBundle:LinkManager:edit.html.twig', [
            'form' => $form->createView(),
            'url' => $url,
        ]);
    }

    /**
     * Renders the view of a URL.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $urlId ID of URL
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $urlId)
    {
        $url = $this->urlService->loadById($urlId);

        $usages = new Pagerfanta(new URLUsagesAdapter($url, $this->urlService));
        $usages->setCurrentPage($request->query->getInt('page', 1));
        $usages->setMaxPerPage($request->query->getInt('limit', self::DEFAULT_MAX_PER_PAGE));

        return $this->render('EzPlatformLinkManagerBundle:LinkManager:view.html.twig', [
            'url' => $url,
            'can_edit' => $this->isGranted(new Attribute('url', 'update')),
            'usages' => $usages,
        ]);
    }

    /**
     * Creates URL list form.
     *
     * @param \EzSystems\EzPlatformLinkManagerBundle\Form\Data\URLListData|null $data
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createListForm(URLListData $data = null)
    {
        return $this->container->get('form.factory')->createNamed('', 'ezplatformlinkmanager_url_list', $data, [
            'method' => Request::METHOD_GET,
            'action' => $this->generateUrl('admin_link_manager_list'),
            'csrf_protection' => false,
        ]);
    }

    /**
     * Creates URL edit form.
     *
     * @param  \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL $url
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(URL $url)
    {
        $data = (new URLMapper())->mapToFormData($url);

        return $this->createForm(URLEditType::class, $data, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('admin_link_manager_edit', [
                'urlId' => $url->id,
            ]),
        ]);
    }

    /**
     * Builds URL criteria from list data.
     *
     * @param \EzSystems\EzPlatformLinkManagerBundle\Form\Data\URLListData $data
     * @return \EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery
     */
    private function buildListQuery(URLListData $data)
    {
        $query = new URLQuery();
        $query->sortClauses = [
            new SortClause\URL(),
        ];

        $criteria = [
            new Criterion\VisibleOnly(),
        ];

        if ($data->searchQuery !== null) {
            $criteria[] = new Criterion\Pattern($data->searchQuery);
        }

        if ($data->status !== null) {
            $criteria[] = new Criterion\Validity($data->status);
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        return $query;
    }
}
