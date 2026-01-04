<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
        .info-box { background: #f0fdf4; padding: 20px; margin: 20px 0; border-left: 4px solid #059669; border-radius: 4px; }
        .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .highlight { color: #059669; font-weight: bold; }
        .badge { display: inline-block; background: #059669; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">🤝 Club Méditerranée</h1>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Proposition de Partenariat - Service Marketing</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $prospection->nom_partenaire }}</strong>,</p>
            
            <p>
                Je me permets de vous contacter au nom du <span class="highlight">Club Méditerranée</span>, 
                leader mondial des vacances tout compris haut de gamme.
            </p>

            <p>
                Nous sommes constamment à la recherche de <strong>partenaires de qualité</strong> pour enrichir 
                l'expérience de nos clients dans nos différents resorts à travers le monde.
            </p>

            @if($prospection->type_activite)
            <p>
                Votre expertise dans le domaine 
                <span class="badge">{{ $prospection->type_activite_label }}</span>
                a particulièrement retenu notre attention.
            </p>
            @endif

            <div class="info-box">
                <h3 style="margin-top: 0; color: #059669;">📋 Notre demande</h3>
                <p style="margin-bottom: 0;">{!! nl2br(e($prospection->message)) !!}</p>
            </div>

            <p>
                <strong>Ce que nous recherchons chez nos partenaires :</strong>
            </p>
            <ul>
                <li>Excellence dans la prestation de services</li>
                <li>Capacité à accueillir des groupes de tailles variées</li>
                <li>Flexibilité et professionnalisme</li>
                <li>Partage de nos valeurs d'hospitalité et de qualité</li>
            </ul>

            <p>
                <strong>Ce que nous offrons :</strong>
            </p>
            <ul>
                <li>Visibilité auprès d'une clientèle internationale premium</li>
                <li>Volume régulier de réservations</li>
                <li>Partenariat structuré et pérenne</li>
                <li>Intégration dans notre réseau mondial de resorts</li>
            </ul>

            <p>
                Nous serions ravis d'échanger avec vous sur les modalités d'un éventuel partenariat. 
                N'hésitez pas à nous répondre directement à cet email ou à nous contacter pour organiser 
                un rendez-vous.
            </p>

            <p style="margin-top: 30px;">
                Dans l'attente de votre retour, je vous prie d'agréer mes salutations distinguées.
            </p>

            <p>
                <strong>{{ $prospection->user->name ?? 'Service Marketing' }}</strong><br>
                <span style="color: #666;">Club Méditerranée - Direction Marketing</span><br>
                <span style="color: #059669;">📧 clubmedsae@gmail.com</span>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">Club Méditerranée S.A.</p>
            <p style="margin: 5px 0 0 0; opacity: 0.7;">Pionnier du concept de vacances tout compris depuis 1950</p>
        </div>
    </div>
</body>
</html>
