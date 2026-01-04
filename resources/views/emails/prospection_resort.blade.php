<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #0066cc 0%, #004a99 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .header img { max-width: 150px; margin-bottom: 15px; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
        .info-box { background: #f8f9fa; padding: 20px; margin: 20px 0; border-left: 4px solid #0066cc; border-radius: 4px; }
        .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .highlight { color: #0066cc; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">🏝️ Club Méditerranée</h1>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Demande d'Information - Service Marketing</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $prospection->nom_resort }}</strong>,</p>
            
            <p>
                Je me permets de vous contacter au nom du <span class="highlight">Club Méditerranée</span>, 
                leader mondial des vacances tout compris haut de gamme.
            </p>

            <p>
                Notre équipe Marketing est actuellement à la recherche de nouveaux partenaires hôteliers 
                pour enrichir notre offre de séjours. Votre établissement a retenu notre attention et nous 
                souhaiterions en savoir plus sur vos prestations.
            </p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #0066cc;">📋 Objet de notre demande</h3>
                <p style="margin-bottom: 0;">{!! nl2br(e($prospection->message)) !!}</p>
            </div>

            <p>
                Nous serions ravis de pouvoir échanger avec vous sur les points suivants :
            </p>
            <ul>
                <li>Présentation de votre établissement et de vos infrastructures</li>
                <li>Types d'hébergements disponibles et capacités</li>
                <li>Services et activités proposés</li>
                <li>Conditions de partenariat éventuelles</li>
            </ul>

            <p>
                N'hésitez pas à nous répondre directement à cet email ou à nous contacter pour organiser 
                un échange téléphonique ou une visite de votre établissement.
            </p>

            <p style="margin-top: 30px;">
                Dans l'attente de votre retour, je vous prie d'agréer mes salutations distinguées.
            </p>

            <p>
                <strong>{{ $prospection->user->name ?? 'Service Marketing' }}</strong><br>
                <span style="color: #666;">Club Méditerranée - Direction Marketing</span><br>
                <span style="color: #0066cc;">📧 clubmedsae@gmail.com</span>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">Club Méditerranée S.A.</p>
            <p style="margin: 5px 0 0 0; opacity: 0.7;">Pionnier du concept de vacances tout compris depuis 1950</p>
        </div>
    </div>
</body>
</html>
