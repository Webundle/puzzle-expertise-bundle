# Puzzle Expertise Bundle

Project based on Symfony project for managing expertise accounts and expertise security.

## **Install bundle**

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```yaml
composer require webundle/puzzle-expertise-bundle
```

## **Step 1: Enable bundle**
Enable admin bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Puzzle\ExpertiseBundle\ExpertiseBundle(),
        );

        // ...
    }

    // ...
}
```

## **Step 2: Configure bundle security**
Configure security by adding it in the `app/config/security.yml` file of your project:

```yaml
security:
   	...
    role_hierarchy:
        ...
        # Expertise
        ROLE_EXPERTISE: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_EXPERTISE]
        
	...
    access_control:
        ...
        # Expertise
        - {path: ^%admin_prefix%expertise, host: "%admin_host%", roles: ROLE_EXPERTISE }

```

## **Step 3: Enable bundle routing**

Register default routes by adding it in the `app/config/routing.yml` file of your project:

```yaml
....
expertise:
    resource: "@ExpertiseBundle/Resources/config/routing.yml"
    prefix:   /
```
See all expertise routes by typing: `php bin/console debug:router | grep expertise`

## **Step 4: Configure bundle**

Configure admin bundle by adding it in the `app/config/config.yml` file of your project:

```yaml
expertise:
    contact:
        enable_mail_channel: true # false if your don't send email
        email_address: 'contact@exemple.com' # sender email
        
admin:
    ...
    modules_available: '..,expertise'
    navigation:
        nodes:
            ...
           
            # Expertise
            expertise:
                label: 'expertise.title'
                description: 'expertise.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-suitcase'
                parent: ~
                user_roles: ['ROLE_EXPERTISE']
            expertise_service:
                label: 'expertise.service.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_service_list'
                sub_paths: ['puzzle_admin_expertise_service_create', 'puzzle_admin_expertise_service_show', 'puzzle_admin_expertise_service_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_feature:
                label: 'expertise.feature.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_feature_list'
                sub_paths: ['puzzle_admin_expertise_feature_create', 'puzzle_admin_expertise_feature_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_project:
                label: 'expertise.project.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_project_list'
                sub_paths: ['puzzle_admin_expertise_project_create', 'puzzle_admin_expertise_project_update', 'puzzle_admin_expertise_project_show']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_staff:
                label: 'expertise.staff.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_staff_list'
                sub_paths: ['puzzle_admin_expertise_staff_create', 'puzzle_admin_expertise_staff_update', 'puzzle_admin_expertise_staff_show']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_pricing:
                label: 'expertise.pricing.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_pricing_list'
                sub_paths: ['puzzle_admin_expertise_pricing_create', 'puzzle_admin_expertise_pricing_update', 'puzzle_admin_expertise_pricing_show']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_partner:
                label: 'expertise.partner.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_partner_list'
                sub_paths: ['puzzle_admin_expertise_partner_create', 'puzzle_admin_expertise_partner_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_testimonial:
                label: 'expertise.testimonial.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_testimonial_list'
                sub_paths: ['puzzle_admin_expertise_testimonial_create', 'puzzle_admin_expertise_testimonial_update', 'puzzle_admin_expertise_testimonial_show']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_faq:
                label: 'expertise.faq.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_faq_list'
                sub_paths: ['puzzle_admin_expertise_faq_create', 'puzzle_admin_expertise_faq_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_request:
                label: 'expertise.contact.navigation'
                translation_domain: 'messages'
                path: 'puzzle_admin_expertise_contact_list'
                sub_paths: ['puzzle_admin_expertise_contact_show']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']

```
