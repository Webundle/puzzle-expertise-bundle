<?php
namespace Puzzle\ExpertiseBundle\Controller;

use Puzzle\ExpertiseBundle\Entity\Service;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\ExpertiseBundle\Form\Type\ServiceCreateType;
use Puzzle\ExpertiseBundle\Form\Type\ServiceUpdateType;
use Puzzle\ExpertiseBundle\Entity\Project;
use Puzzle\ExpertiseBundle\Form\Type\ProjectCreateType;
use Puzzle\ExpertiseBundle\Form\Type\ProjectUpdateType;
use Puzzle\ExpertiseBundle\Entity\Staff;
use Puzzle\ExpertiseBundle\Form\Type\StaffCreateType;
use Puzzle\ExpertiseBundle\Form\Type\StaffUpdateType;
use Puzzle\ExpertiseBundle\Entity\Partner;
use Puzzle\ExpertiseBundle\Form\Type\PartnerCreateType;
use Puzzle\ExpertiseBundle\Form\Type\PartnerUpdateType;
use Puzzle\ExpertiseBundle\Entity\Testimonial;
use Puzzle\ExpertiseBundle\Form\Type\TestimonialCreateType;
use Puzzle\ExpertiseBundle\Form\Type\TestimonialUpdateType;
use Puzzle\ExpertiseBundle\Entity\Faq;
use Puzzle\ExpertiseBundle\Form\Type\FaqCreateType;
use Puzzle\ExpertiseBundle\Form\Type\FaqUpdateType;
use Puzzle\ExpertiseBundle\Entity\Feature;
use Symfony\Component\HttpFoundation\JsonResponse;
use Puzzle\ExpertiseBundle\Form\Type\FeatureCreateType;
use Puzzle\ExpertiseBundle\Form\Type\FeatureUpdateType;
use Puzzle\ExpertiseBundle\Entity\Pricing;
use Puzzle\ExpertiseBundle\Form\Type\PricingCreateType;
use Puzzle\ExpertiseBundle\Form\Type\PricingUpdateType;
use Puzzle\ExpertiseBundle\Form\Type\ProjectUpdateGalleryType;
use Puzzle\ExpertiseBundle\Entity\Contact;
use Doctrine\ORM\EntityManager;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * Show Services
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listServicesAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_services.html.twig", array(
            'services' => $this->getDoctrine()->getRepository(Service::class)->findAll()
        ));
    }
    
    /***
     * Show service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showServiceAction(Request $request, Service $service) {
        return $this->render("ExpertiseBundle:Admin:show_service.html.twig", [
            'service' => $service,
            'childNodes' => $service->getChildNodes()
        ]);
    }
    
    /***
     * Create service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createServiceAction(Request $request) {
        $service = new Service();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if (false === empty($parentId)) {
            $parent = $em->getRepository(Service::class)->find($parentId);
            $service->setParentNode($parent);
        }
        
        $form = $this->createForm(ServiceCreateType::class, $service, [
            'method' => 'POST',
            'action' => false === empty($parentId) ? 
                        $this->generateUrl('puzzle_admin_expertise_service_create', ['parent' => $parentId]) :
                        $this->generateUrl('puzzle_admin_expertise_service_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_service_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Service::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($service) {
                        $service->setPicture($filename);
                    }
                 ]));
            }
            
            $em->persist($service);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.service.create.success', [
                '%serviceName%' => $service->getName()
            ], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_service_show', ['id' => $service->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:create_service.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    /***
     * Update service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateServiceAction(Request $request, Service $service) {
        $form = $this->createForm(ServiceUpdateType::class, $service, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_service_update', ['id' => $service->getId()])
        ]);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_service_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($service->getPicture() === null || $service->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Service::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($service) {
                        $service->setPicture($filename);
                    }
                ]));
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.service.update.success', [
                '%serviceName%' => $service->getName()
            ], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_service_show', ['id' => $service->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:update_service.html.twig", array(
            'service' => $service,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteServiceAction(Request $request, Service $service) {
        $message = $this->get('translator')->trans('expertise.service.delete.success', [
            '%serviceName%' => $service->getName()
        ], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $em->flush();
        
        if (true === $request->isXmlHttpRequest()) {
            
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_service_list');
    }
    
    /***
     * Show Projects
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listProjectsAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_projects.html.twig", array(
            'projects' => $this->getDoctrine()->getRepository(Project::class)->findAll()
        ));
    }
    
    /***
     * Show Project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProjectAction(Request $request, Project $project) {
        return $this->render("ExpertiseBundle:Admin:show_project.html.twig", array(
            'project' => $project
        ));
    }
    
    /**
     *
     * Create project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createProjectAction(Request $request) {
        $project = new Project();
        $form = $this->createForm(ProjectCreateType::class, $project, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_project_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_project_create'];
            
            $project->setStartedAt($data['startedAt']);
            $project->setEndedAt($data['endedAt']);
            
            $pictures = $request->request->get('pictures') !== null ? $request->request->get('pictures') : $data['pictures'];
            
            if ($pictures !== null) {
                $project->setPictures([]);
                $pictures = explode(',', $pictures);
                
                foreach ($pictures as $picture) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $picture,
                        'context' => MediaUtil::extractContext(Project::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($project) {
                            $project->addPicture($filename);
                        }
                    ]));
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.project.create.success', ['%projectName%' => $project->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_project_show', ['id' => $project->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:create_project.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    /**
     *
     * Update project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateProjectAction(Request $request, Project $project) {
        $form = $this->createForm(ProjectUpdateType::class, $project, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_project_update', ['id' => $project->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_project_update'];
            
            $project->setStartedAt($data['startedAt']);
            $project->setEndedAt($data['endedAt']);
            
            $pictures = $request->request->get('pictures') !== null ? $request->request->get('pictures') : $data['pictures'];
            
            if ($pictures !== null) {
                $pictures = explode(',', $pictures);
                $project->setPictures($pictures);
                
                foreach ($pictures as $picture) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $picture,
                        'context' => MediaUtil::extractContext(Project::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($project) {
                            $project->addPicture($filename);
                        }
                    ]));
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.project.update.success', ['%projectName%' => $project->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_project_show', ['id' => $project->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:update_project.html.twig", array(
            'project'  => $project,
            'form'     => $form->createView()
        ));
    }
    
    /***
     * Delete Project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProjectAction(Request $request, Project $project) {
        $message = $this->get('translator')->trans('expertise.project.delete.success', ['%projectName%' => $project->getName()], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_project_list');
    }
    
    /***
     * Show Staffs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listStaffsAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_staffs.html.twig", array(
            'staffs' => $this->getDoctrine()->getRepository(Staff::class)->findAll()
        ));
    }
    
    /***
    * Show Staff
    *
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function showStaffAction(Request $request, Staff $staff) {
        return $this->render("ExpertiseBundle:Admin:show_staff.html.twig", array(
            'staff' => $staff
        ));
    }
    
    /***
     * Create staff
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createStaffAction(Request $request) {
        $staff = new Staff();
        
        $form = $this->createForm(StaffCreateType::class, $staff, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_staff_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_staff_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Staff::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($staff) {$staff->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($staff);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.staff.create.success',  ['%staff%' => (string)$staff], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_staff_show', ['id' => $staff->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:create_staff.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    /***
            
     * Update staff
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateStaffAction(Request $request, Staff $staff) {
        $form = $this->createForm(StaffUpdateType::class, $staff, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_staff_update', ['id' => $staff->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_staff_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($staff->getPicture() === null || $staff->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Staff::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($staff) {$staff->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.staff.update.success', ['%staff%' => (string)$staff], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_staff_show', ['id' => $staff->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:update_staff.html.twig", array(
            'staff' => $staff,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete staff
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteStaffAction(Request $request, Staff $staff) {
        $message = $this->get('translator')->trans('expertise.staff.delete.success', ['%staff%' => (string)$staff], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($staff);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_staff_list');
    }
    
    /***
     * Show Partners
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPartnersAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_partners.html.twig", array(
            'partners' => $this->getDoctrine()->getRepository(Partner::class)->findAll()
        ));
    }
    
    /***
     * Show Partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPartnerAction(Request $request, Partner $partner) {
        return $this->render("ExpertiseBundle:Admin:show_partner.html.twig", array(
            'partner' => $partner
        ));
    }
    
    /***
     * Create partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPartnerAction(Request $request){
        $partner = new Partner();
        
        $form = $this->createForm(PartnerCreateType::class, $partner, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_partner_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $partner->setTags($partner->getTags() !== null ? explode(',', $partner->getTags()) : null);
            $data = $request->request->all()['puzzle_admin_expertise_partner_create'];
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Partner::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($partner) {$partner->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($partner);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.partner.create.success', ['%partnerName%' => $partner->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_partner_list');
        }
        
        return $this->render("ExpertiseBundle:Admin:create_partner.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePartnerAction(Request $request, Partner $partner) {
        $form = $this->createForm(PartnerUpdateType::class, $partner, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_partner_update', ['id' => $partner->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $partner->setTags($partner->getTags() !== null ? explode(',', $partner->getTags()) : null);
            
            $data = $request->request->all()['puzzle_admin_expertise_partner_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($partner->getPicture() === null || $partner->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Partner::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($partner) {$partner->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.partner.update.success', ['%partnerName%' => $partner->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_partner_list');
        }
        
        return $this->render("ExpertiseBundle:Admin:update_partner.html.twig", array(
            'partner' => $partner,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePartnerAction(Request $request, Partner $partner) {
        $message = $this->get('translator')->trans('expertise.partner.delete.success', ['%partnerName%' => $partner->getName()], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($partner);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_partner_list');
    }
    
    /***
     * Show testimonials
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTestimonialsAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_testimonials.html.twig", array(
            'testimonials' => $this->getDoctrine()->getRepository(Testimonial::class)->findAll()
        ));
    }
    
    /***
     * Show testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTestimonialAction(Request $request, Testimonial $testimonial) {
        return $this->render("ExpertiseBundle:Admin:show_testimonial.html.twig", array(
            'testimonial' => $testimonial
        ));
    }
    
    /***
     * Create testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createTestimonialAction(Request $request) {
        $testimonial = new Testimonial();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(TestimonialCreateType::class, $testimonial, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_testimonial_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_testimonial_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Testimonial::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($testimonial) {$testimonial->setPicture($filename);}
                ]));
            }
            
            $em->persist($testimonial);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.testimonial.create.success', ['%testimonialAuthor%' => $testimonial->getAuthor()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_testimonial_show', ['id' => $testimonial->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:create_testimonial.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    /***
     * Update testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateTestimonialAction(Request $request, Testimonial $testimonial)
    {
        $form = $this->createForm(TestimonialUpdateType::class, $testimonial, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_testimonial_update', ['id' => $testimonial->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_expertise_testimonial_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($testimonial->getPicture() === null || $testimonial->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $testimonial->getPicture(),
                    'context' => MediaUtil::extractContext(Testimonial::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($testimonial) {
                        $testimonial->setPicture($filename);
                    }
                 ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.testimonial.update.success', ['%testimonialAuthor%' => $testimonial->getAuthor()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_testimonial_show', ['id' => $testimonial->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:update_testimonial.html.twig", array(
            'testimonial' => $testimonial,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteTestimonialAction(Request $request, Testimonial $testimonial) {
        $message = $this->get('translator')->trans('success.put', ['%item%' => $testimonial->getAuthor()], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($testimonial);
        $em->flush();
        
        if  ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_testimonial_list');
    }
    
    
    /***
     * Show FAQs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFaqsAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_faqs.html.twig", array(
            'faqs' => $this->getDoctrine()->getRepository(Faq::class)->findAll()
        ));
    }
    
    /***
     * Show FAQ
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFaqAction(Request $request, Faq $faq) {
        return $this->render("ExpertiseBundle:Admin:show_faq.html.twig", array(
            'faq' => $faq
        ));
    }
    
    /***
     * Create Faq
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFaqAction(Request $request) {
        $faq = new Faq();
        $form = $this->createForm(FaqCreateType::class, $faq, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_faq_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.faq.create.success', ['%faq%' => $faq->getQuestion()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_faq_list', ['id' => $faq->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:create_faq.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update Faq Form
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFaqAction(Request $request, Faq $faq) {
        $form = $this->createForm(FaqUpdateType::class, $faq, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_faq_update', ['id' => $faq->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.faq.update.success', ['%faq%' => $faq->getQuestion()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_faq_list', ['id' => $faq->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:update_faq.html.twig", [
            'faq' => $faq,
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Delete Faq
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFaqAction(Request $request, Faq $faq) {
        $message = $this->get('translator')->trans('expertise.faq.delete.success', ['%faq%' => $faq->getQuestion()], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($faq);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_faq_list');
    }
    
    /***
     * List features
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFeaturesAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_features.html.twig", array(
            'features' => $this->getDoctrine()->getRepository(Feature::class)->findAll()
        ));
    }
    
    /***
     * Show feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFeatureAction(Request $request, Feature $feature) {
        return $this->render("ExpertiseBundle:Admin:show_feature.html.twig", array(
            'feature' => $feature
        ));
    }
    
    /***
     * Create feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFeatureAction(Request $request) {
        $feature = new Feature();
        $form = $this->createForm(FeatureCreateType::class, $feature, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_feature_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feature);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $feature->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_feature_list');
        }
        
        return $this->render("ExpertiseBundle:Admin:create_feature.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFeatureAction(Request $request, Feature $feature) {
        $form = $this->createForm(FeatureUpdateType::class, $feature, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_feature_update', ['id' => $feature->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $feature->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_feature_list');
        }
        
        return $this->render("ExpertiseBundle:Admin:update_feature.html.twig", [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Delete feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFeatureAction(Request $request, Feature $feature) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $feature->getName()], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($feature);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_feature_list');
    }
    
    /***
     * List pricings
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPricingsAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_pricings.html.twig", array(
            'pricings' => $this->getDoctrine()->getRepository(Pricing::class)->findAll()
        ));
    }
    
    /***
    * Show pricing
    *
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function showPricingAction(Request $request, Pricing $pricing) {
        return $this->render("ExpertiseBundle:Admin:show_pricing.html.twig", array(
            'pricing' => $pricing
        ));
    }
    
    /***
     * Create pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPricingAction(Request $request) {
        $pricing = new Pricing();
        $form = $this->createForm(PricingCreateType::class, $pricing, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_pricing_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pricing);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.pricing.create.success', ['%pricingName%' => $pricing->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_pricing_show', ['id' => $pricing->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:create_pricing.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePricingAction(Request $request, Pricing $pricing) {
        $form = $this->createForm(PricingUpdateType::class, $pricing, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_expertise_pricing_update', ['id' => $pricing->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('expertise.pricing.update.success', ['%pricingName%' => $pricing->getName()], 'expertise'));
            return $this->redirectToRoute('puzzle_admin_expertise_pricing_show', ['id' => $pricing->getId()]);
        }
        
        return $this->render("ExpertiseBundle:Admin:update_pricing.html.twig", [
            'pricing' => $pricing,
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Delete pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePricingAction(Request $request, Pricing $pricing) {
        $message = $this->get('translator')->trans('expertise.pricing.delete.success', ['%pricingName%' => $pricing->getName()], 'expertise');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($pricing);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_pricing_list');
    }
    
    
    /***
     * Show contacts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listContactsAction(Request $request) {
        return $this->render("ExpertiseBundle:Admin:list_contacts.html.twig", array(
            'contacts' => $this->getDoctrine()->getRepository(Contact::class)->findBy([], [
                'markAsRead' => 'ASC',
                'createdAt' => 'DESC',
            ]),
        ));
    }
    
    /***
     * Show contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showContactAction(Request $request, $id) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $contact = $em->find(Contact::class, $id);
        
        if ($contact) {
            $contact->markAsRead();
            $contact->setReadAt(new \DateTime());
            
            $em->flush();
        }
        
        return $this->render("ExpertiseBundle:Admin:show_contact.html.twig", array(
            'contact' => $contact
        ));
    }
    
    /**
     * Delete a contact
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContactAction(Request $request, $id) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $contact = $em->find(Contact::class, $id);
        
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $contact->getSubject()], 'expertise');
        
        $em->remove($contact);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_expertise_contact_list');
    }
}
