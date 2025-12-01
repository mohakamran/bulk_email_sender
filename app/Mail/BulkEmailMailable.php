<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulkEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $university;
    public $research;
    public $cvPath;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $university, $research, $cvPath)
    {
        $this->name = $name;
        $this->university = $university;
        $this->research = $research;
        $this->cvPath = $cvPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '博士課程指導に関するお問い合わせ - Inquiry About PhD Supervision',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailContent = $this->buildEmailContent();
        
        $email = $this->subject('博士課程指導に関するお問い合わせ - Inquiry About PhD Supervision')
                    ->text('emails.plain-text')
                    ->with(['content' => $emailContent]);
        
        // Add attachment if CV exists
        if ($this->cvPath) {
            $fullPath = storage_path('app/public/' . $this->cvPath);
            
            if (file_exists($fullPath)) {
                $email->attach($fullPath, [
                    'as' => 'CV.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }
        
        return $email;
    }

    private function buildEmailContent()
    {
        return "Respected Professor {$this->name},

私の名前はムハンマド・カムランと申します。現在、日本の北九州市立大学にて文部科学省奨学生として応用情報システムの修士号を取得中です。このたび、貴殿のご指導のもとで博士号取得を目指したいと考えておりますことをお知らせいたします。

貴殿の{$this->research}に関する研究に大変興味があり、この分野で学び、研究を進めたいと強く願っております。貴殿の研究アプローチと業績に深く感銘を受け、私の学術的目標と研究関心が貴研究室の方向性と完全に一致していると感じております。

交通システムにおける人的要素をモデル化し検証するため、現在の研究では運転者行動と交通心理学に焦点を当てています。2026年9月の修士課程修了までに、このテーマに関する研究報告書を提出する予定です。また、研究室パートナーと共同で音響学に関する研究を進めており、その論文の共著者でもあります。

パキスタンでソフトウェアエンジニアとして2年8ヶ月間勤務した経験を通じて、ソフトウェア開発、問題解決、応用コンピューティングに関する豊富な専門知識を習得しました。その後、日本で修士課程を開始しました。これまでのキャリアと現在の研究経験により、技術的・分析的スキルが向上しており、これらを人工知能（AI）、機械学習（ML）、深層学習（ディープラーニング）分野における博士論文の研究に活かしていく所存です。

貴研究室における博士課程研究候補者向けの資金調達機会について、ご教示いただければ大変ありがたく存じます。お時間を割いてご検討いただき、心より感謝申し上げます。貴研究室での博士課程の可能性について議論するため、簡単なオンライン面談の手配や、成績証明書・研究計画書などの追加資料の提供を喜んで承ります。

My name is Muhammad Kamran, and I am presently a MEXT scholar at the University of Kitakyushu in Japan, where I am pursuing a Masters degree in Applied Information Systems. I am writing to let you know that I would like to pursue a PhD under your guidance.

I am very interested in learning and conducting research in your {$this->research} field. I am deeply impressed by your research approach and achievements, and I feel that my academic goals and research interests align perfectly with your lab  direction.

In order to model and examine human elements in traffic systems, my current study focuses on driver behavior and traffic psychology. Prior to finishing my masters degree in September 2026, I intend to turn in a research report on this subject. I am also co-authoring a study on acoustics that I am working on with a lab partner.

I gained a great deal of expertise in software development, problem-solving, and applied computing during my 2.8 years as a software engineer in Pakistan before to beginning my master's degree in Japan. My career background and current research experience have enhanced my technical and analytical skills, which I intend to use to a PhD thesis in AI, ML, or deep learning.

If you could inform me of any funding opportunities for PhD research candidate in your lab, I would be very appreciative. I sincerely appreciate your time and thought. In order to discuss possible PhD prospects in your lab, I would be pleased to arrange a brief online discussion or give any further materials such as transcripts or research statement.

Sincerely,
カムラン ムハンマド - Kamran Muhammad
M.Sc. Applied Information Systems, University of Kitakyushu, Japan
Portfolio: https://mohakamran.github.io/portfolio/
f4mcb401@eng.kitakyu-u.ac.jp

---
This message was sent by kitakyushu software.";
    }
}
