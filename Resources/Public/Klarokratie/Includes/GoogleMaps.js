(function() {
  if (
    typeof klaroConfig === 'object' &&
    Array.isArray(klaroConfig.services)
  ) {
    const additionalService = {
      name: "gadgetoGoogle",
      purposes: ['multimedia'],
      // contextualConsentOnly: true,
      cookies: [
        /^NID/, // we delete the cookies if the user declines its use
        /^_Secure-ENID/,
        /^SID/,
        /^HSID/,
        /^AEC/,
        /^IDE/,
        /^DSID/,
        /^1P_JAR/,
        /^CONSENT/,
      ],
      translations: {
        zz: {
          title: 'Google Maps'
        },
        en: {
          description: 'We use the Google Maps service (Maps JavaScript API) provided by Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Ireland, email: <a href="mailto:support-deutschland@google.com">support-deutschland@google.com</a>, website: <a href="https://www.google.com/" target="_blank" rel="noopener">https://www.google.com/</a>. If Google Maps is activated on our website (e.g. after consent) and the map is loaded, our website establishes a connection to the servers of Google Ireland Limited and transmits the data required to display the map (in particular IP address, device/browser information, referrer URL and, if applicable, location data – depending on your device settings). In doing so, Google may store or read cookies and similar technologies on your device in order to save preferences, provide security features and (depending on your settings) support advertising. When integrating Google Maps via the Maps JavaScript API, fonts (Google Fonts) are usually downloaded from Google Ireland Limited servers in order to display the map labels and UI elements correctly. Among other things, the IP address is transmitted to Google in the \n' +
            klarokratieGetTableHtml('Cookie:', 'gadgetogoogle-consent', 'Dauer:', 'Session') +
            klarokratieGetTableHtml('Cookie:', 'NID', 'Duration:', 'approx. 6 Monate') +
            klarokratieGetTableHtml('Cookie:', '_Secure-ENID', 'Duration:', 'approx. 1 Jahr') +
            klarokratieGetTableHtml('Cookie:', 'SOCS', 'Duration:', 'approx. 1 Jahr') +
            klarokratieGetTableHtml('Cookie:', 'SID', 'Duration:', 'approx. 2 Jahre') +
            klarokratieGetTableHtml('Cookie:', 'HSID', 'Duration:', 'approx. 2 Jahre') +
            klarokratieGetTableHtml('Cookie:', 'AEC', 'Duration:', 'approx. 6 Monate') +
            klarokratieGetTableHtml('Cookie:', 'IDE', 'Duration:', 'approx.  1 Jahr') +
            klarokratieGetTableHtml('Cookie:', 'DSID', 'Duration:', 'approx. 14 Tage') +
            klarokratieGetTableHtml('Cookie:', '1P_JAR', 'Duration:', 'approx. 1 Monat') +
            klarokratieGetTableHtml('Cookie:', 'CONSENT', 'Duration:', 'approx. 2 Jahre')
        },
        de: {
          description: 'Wir verwenden auf unserer Seite den Dienst Google Maps (Maps JavaScript API) des Unternehmens Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland, E-Mail: <a href="mailto:support-deutschland@google.com">support-deutschland@google.com</a>, Website: <a href="https://www.google.com/" target="_blank" rel="noopener">https://www.google.com/</a>. Sofern Google Maps auf unserer Website aktiv geschaltet ist (z. B. nach Einwilligung) und die Karte geladen wird, stellt unsere Website eine Verbindung zu den Servern des Unternehmens Google Ireland Limited her und überträgt die für die Darstellung der Karte erforderlichen Daten (insbesondere IP-Adresse, Geräte-/Browserinformationen, Referrer-URL sowie ggf. Standortdaten – abhängig von Ihren Geräteeinstellungen). Dabei können durch Google Cookies und ähnliche Technologien auf Ihrem Endgerät gespeichert bzw. ausgelesen werden, um Präferenzen zu speichern, Sicherheitsfunktionen bereitzustellen und (abhängig von Ihren Einstellungen) Werbung zu unterstützen. Bei der Einbindung von Google Maps über die Maps JavaScript API werden in der Regel Schriftarten (Google Fonts) von Servern von Google Ireland Limited nachgeladen, um die Kartenbeschriftungen und UI-Elemente korrekt darzustellen. Dabei wird u. a. die IP-Adresse an Google übermittelt.\n' +
            klarokratieGetTableHtml('Cookie:', 'gadgetogoogle-consent', 'Dauer:', 'Sitzung') +
            klarokratieGetTableHtml('Cookie:', 'NID', 'Dauer:', 'ca. 6 Monate') +
            klarokratieGetTableHtml('Cookie:', '_Secure-ENID', 'Dauer:', 'ca. 1 Jahr') +
            klarokratieGetTableHtml('Cookie:', 'SOCS', 'Dauer:', 'ca. 1 Jahr') +
            klarokratieGetTableHtml('Cookie:', 'SID', 'Dauer:', 'ca. 2 Jahre') +
            klarokratieGetTableHtml('Cookie:', 'HSID', 'Dauer:', 'ca. 2 Jahre') +
            klarokratieGetTableHtml('Cookie:', 'AEC', 'Dauer:', 'ca. 6 Monate') +
            klarokratieGetTableHtml('Cookie:', 'IDE', 'Dauer:', 'ca.  1 Jahr') +
            klarokratieGetTableHtml('Cookie:', 'DSID', 'Dauer:', 'ca. 14 Tage') +
            klarokratieGetTableHtml('Cookie:', '1P_JAR', 'Dauer:', 'ca. 1 Monat') +
            klarokratieGetTableHtml('Cookie:', 'CONSENT', 'Dauer:', 'ca. 2 Jahre')
        },
      },
      onInit: ``,
      onAccept: `
        document.addEventListener('DOMContentLoaded', () => {
          document.dispatchEvent(
            new CustomEvent('gadgetoGoogle:consent:given');
          );
        });
      `,
      onDecline: `
        document.addEventListener('DOMContentLoaded', () => {
          document.dispatchEvent(
            document.dispatchEvent(new CustomEvent('gadgetoGoogle:consent:revoked');
          );
        });
      `
    };

    if (!klaroConfig.services.find(service => service.name === additionalService.name)) {
      klaroConfig.services.push(additionalService);
    }
  }
})();
