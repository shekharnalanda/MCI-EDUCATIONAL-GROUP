<?php

namespace Database\Seeders;

use App\Models\AutoReplyRule;
use App\Models\Institution;
use App\Models\ReplyTemplate;
use Illuminate\Database\Seeder;

class InitialAutoReplySeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'course_fee' => [
                'label' => 'Course Fee Enquiry',
                'keywords' => ['fee','fees','course fee','फीस','शुल्क','kitna lagega','kitna paisa','कितना लगेगा','कितना पैसा','cost','charge','price'],
                'body' => "नमस्कार {customer_name},\n\n{business_name} से संपर्क करने के लिए धन्यवाद। आपने {course_name} की फीस/शुल्क के संबंध में जानकारी मांगी है। आपकी enquiry प्राप्त हो गई है। वर्तमान और सत्यापित फीस की जानकारी संबंधित team द्वारा उपलब्ध कराई जाएगी।\n\nअधिक जानकारी: {contact_number}\n{website_url}\n\nधन्यवाद\n{business_name}\nMCI Educational Group",
            ],
            'admission' => [
                'label' => 'Admission Enquiry',
                'keywords' => ['admission','admit','enroll','enrollment','join','registration','प्रवेश','दाखिला','एडमिशन','admission kaise','form kaise'],
                'body' => "नमस्कार {customer_name},\n\n{business_name} में admission संबंधी आपकी enquiry प्राप्त हो गई है। Admission process, required documents और उपलब्ध विकल्पों की सही जानकारी संबंधित team द्वारा दी जाएगी।\n\nअधिक जानकारी: {contact_number}\n{website_url}\n\nधन्यवाद\n{business_name}\nMCI Educational Group",
            ],
            'duration' => [
                'label' => 'Duration Enquiry',
                'keywords' => ['duration','course duration','kitne month','kitna month','कितने महीने','अवधि','how long','time period'],
                'body' => "नमस्कार {customer_name},\n\nआपने {business_name} में {course_name} की अवधि के संबंध में जानकारी मांगी है। आपकी enquiry प्राप्त हो गई है। सही course/service duration संबंधित current record के अनुसार team द्वारा confirm की जाएगी।\n\nअधिक जानकारी: {contact_number}\n{website_url}\n\nधन्यवाद\n{business_name}",
            ],
            'timing' => [
                'label' => 'Timing Enquiry',
                'keywords' => ['timing','time','batch time','class time','shift','समय','टाइमिंग','batch kab','class kab'],
                'body' => "नमस्कार {customer_name},\n\n{business_name} की timing/batch/shift संबंधी आपकी enquiry प्राप्त हो गई है। वर्तमान उपलब्ध timing संबंधित team द्वारा confirm की जाएगी।\n\nअधिक जानकारी: {contact_number}\n{website_url}\n\nधन्यवाद\n{business_name}",
            ],
            'location' => [
                'label' => 'Location Enquiry',
                'keywords' => ['address','location','where','kaha','kahan','कहाँ','पता','लोकेशन','campus address','office address'],
                'body' => "नमस्कार {customer_name},\n\nआपने {business_name} के address/location के बारे में पूछा है। अधिक जानकारी और current location details के लिए: {contact_number}\n{website_url}\n\nधन्यवाद\n{business_name}\nMCI Educational Group",
            ],
            'general' => [
                'label' => 'General Enquiry',
                'keywords' => ['information','details','help','जानकारी','detail','बताइए','bataiye','contact','support','enquiry','query'],
                'body' => "नमस्कार {customer_name},\n\n{business_name} से संपर्क करने के लिए धन्यवाद। आपकी enquiry हमें प्राप्त हो गई है। आपके प्रश्न की सही जानकारी संबंधित team द्वारा उपलब्ध कराई जाएगी।\n\nअधिक जानकारी: {contact_number}\n{website_url}\n\nधन्यवाद\n{business_name}\nMCI Educational Group",
            ],
        ];

        Institution::query()->where('is_active', true)->orderBy('id')->each(function (Institution $institution) use ($definitions) {
            foreach ($definitions as $category => $definition) {
                $template = ReplyTemplate::updateOrCreate(
                    [
                        'institution_id' => $institution->id,
                        'name' => $institution->name.' - '.$definition['label'],
                    ],
                    [
                        'category' => $category,
                        'language' => 'hi',
                        'subject' => $institution->name.' - '.$definition['label'],
                        'body' => $definition['body'],
                        'placeholders' => ['customer_name','business_name','course_name','contact_number','website_url'],
                        'version' => 1,
                        'status' => 'approved',
                        'is_active' => true,
                    ]
                );

                AutoReplyRule::updateOrCreate(
                    [
                        'institution_id' => $institution->id,
                        'name' => $institution->name.' - '.$definition['label'].' Rule',
                    ],
                    [
                        'reply_template_id' => $template->id,
                        'category' => $category,
                        'keywords' => $definition['keywords'],
                        'conditions' => null,
                        'priority' => $category === 'general' ? 900 : 100,
                        'auto_send' => false,
                        'is_active' => true,
                        'fallback_action' => 'manual_review',
                    ]
                );
            }

            if ($institution->auto_reply_enabled) {
                $institution->update(['auto_reply_enabled' => false]);
            }
        });
    }
}
