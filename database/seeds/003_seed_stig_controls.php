<?php

/**
 * Seed STIG (Security Technical Implementation Guide) references
 * This is a representative sample of common STIG items
 */

return function($db) {
    $controls = [
        // Windows 10 STIG
        ['framework' => 'STIG', 'code' => 'WN10-00-000010', 'title' => 'Systems must have Trusted Platform Module (TPM) enabled', 'description' => 'Trusted Platform Module (TPM) is a hardware-based security feature that can be used to create and manage computer-generated encryption keys.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN10-00-000015', 'title' => 'Data Execution Prevention (DEP) must be configured', 'description' => 'Data Execution Prevention (DEP) must be configured to at least OptOut.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'high'],
        ['framework' => 'STIG', 'code' => 'WN10-00-000020', 'title' => 'Structured Exception Handling Overwrite Protection (SEHOP) must be enabled', 'description' => 'Structured Exception Handling Overwrite Protection (SEHOP) blocks exploits that use the Structured Exception Handling overwrite technique.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN10-AC-000010', 'title' => 'The number of allowed bad logon attempts must be configured', 'description' => 'The account lockout threshold must be configured to 3 or fewer invalid logon attempts.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN10-AC-000015', 'title' => 'The period of time before the bad logon counter is reset must be configured', 'description' => 'The reset account lockout counter must be configured to at least 15 minutes.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN10-AC-000020', 'title' => 'Account lockout duration must be configured', 'description' => 'The account lockout duration must be configured to 15 minutes or greater.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN10-CC-000020', 'title' => 'AutoPlay must be turned off for non-volume devices', 'description' => 'Allowing AutoPlay to execute may introduce malicious code to a system.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'high'],
        ['framework' => 'STIG', 'code' => 'WN10-CC-000030', 'title' => 'The default AutoRun behavior must be configured to prevent AutoRun commands', 'description' => 'Allowing AutoRun commands to execute may introduce malicious code to a system.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'high'],
        ['framework' => 'STIG', 'code' => 'WN10-SO-000015', 'title' => 'Audit policy using subcategories must be enabled', 'description' => 'Maintaining an audit trail of system activity logs can help identify configuration errors, troubleshoot service disruptions, and analyze compromises.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN10-SO-000020', 'title' => 'Command line data must be included in process creation events', 'description' => 'Maintaining an audit trail of system activity logs can help identify configuration errors, troubleshoot service disruptions, and analyze compromises that have occurred.', 'stig_version' => 'Windows 10 V2R8', 'stig_severity' => 'medium'],

        // Windows Server STIG
        ['framework' => 'STIG', 'code' => 'WN19-00-000010', 'title' => 'Domain controllers must require LDAP access signing', 'description' => 'Unsigned network traffic is susceptible to man-in-the-middle attacks.', 'stig_version' => 'Windows Server 2019 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN19-DC-000010', 'title' => 'Windows Server 2019 domain controllers must have a PKI server certificate', 'description' => 'Domain controllers are part of the Private Key Infrastructure (PKI) and require a server certificate.', 'stig_version' => 'Windows Server 2019 V2R8', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'WN19-SO-000100', 'title' => 'Kerberos encryption types must be configured to prevent the use of DES and RC4', 'description' => 'Certain encryption types are no longer considered secure.', 'stig_version' => 'Windows Server 2019 V2R8', 'stig_severity' => 'medium'],

        // Network Device STIG
        ['framework' => 'STIG', 'code' => 'NET-FW-001', 'title' => 'The network device must authenticate all network-connected endpoint devices', 'description' => 'Device authentication is a solution enabling an organization to manage devices.', 'stig_version' => 'Network Device V2R11', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'NET-FW-002', 'title' => 'The network device must use multifactor authentication for network access', 'description' => 'Multifactor authentication creates a layered defense and makes it more difficult for an unauthorized person to access the network device.', 'stig_version' => 'Network Device V2R11', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'NET-FW-003', 'title' => 'The network device must terminate all network connections associated with a device management session at the end of the session', 'description' => 'Terminating an idle session within a short time period reduces the window of opportunity for unauthorized personnel to take control of a management session.', 'stig_version' => 'Network Device V2R11', 'stig_severity' => 'medium'],

        // Application Security STIG
        ['framework' => 'STIG', 'code' => 'APSC-DV-000010', 'title' => 'Applications must obscure feedback of authentication information', 'description' => 'Applications must obscure feedback of authentication information during the authentication process to protect the information from possible exploitation/use by unauthorized individuals.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'APSC-DV-000160', 'title' => 'The application must enforce approved authorizations', 'description' => 'To mitigate the risk of unauthorized access to sensitive information, the application must enforce approved authorizations.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'high'],
        ['framework' => 'STIG', 'code' => 'APSC-DV-000500', 'title' => 'The application must generate audit records when successful/unsuccessful attempts to access security objects occur', 'description' => 'Without generating audit records specific to the security and mission needs of the organization, it would be difficult to establish, correlate, and investigate the events relating to an incident.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'APSC-DV-002010', 'title' => 'The application must protect audit information from unauthorized read access', 'description' => 'Unauthorized disclosure of audit records can reveal system and configuration data to attackers.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'APSC-DV-002020', 'title' => 'The application must protect audit information from unauthorized modification', 'description' => 'If audit data were to become compromised, then forensic analysis and discovery of the true source of potentially malicious system activity is impossible to achieve.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'APSC-DV-002030', 'title' => 'The application must protect audit information from unauthorized deletion', 'description' => 'If audit data were to become compromised, then forensic analysis and discovery of the true source of potentially malicious system activity is impossible to achieve.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'APSC-DV-002560', 'title' => 'The application must protect the confidentiality and integrity of transmitted information', 'description' => 'Without protection of the transmitted information, confidentiality and integrity may be compromised.', 'stig_version' => 'Application Security V5R3', 'stig_severity' => 'high'],

        // Database STIG
        ['framework' => 'STIG', 'code' => 'SRG-APP-000001-DB-000001', 'title' => 'The DBMS must limit the number of concurrent sessions', 'description' => 'Database management includes the ability to control the number of users and user sessions utilizing a DBMS.', 'stig_version' => 'Database V9R6', 'stig_severity' => 'low'],
        ['framework' => 'STIG', 'code' => 'SRG-APP-000033-DB-000084', 'title' => 'The DBMS must enforce approved authorizations for logical access', 'description' => 'Strong access controls are critical to securing data.', 'stig_version' => 'Database V9R6', 'stig_severity' => 'high'],
        ['framework' => 'STIG', 'code' => 'SRG-APP-000095-DB-000039', 'title' => 'The DBMS must protect audit data from unauthorized read access', 'description' => 'Unauthorized disclosure of audit records can reveal system and configuration data to attackers.', 'stig_version' => 'Database V9R6', 'stig_severity' => 'medium'],

        // Web Server STIG
        ['framework' => 'STIG', 'code' => 'SRG-APP-000014-WSR-000006', 'title' => 'The web server must limit the number of allowed simultaneous session requests', 'description' => 'Resource exhaustion can occur when an unlimited number of concurrent requests are allowed on a website.', 'stig_version' => 'Web Server V2R3', 'stig_severity' => 'medium'],
        ['framework' => 'STIG', 'code' => 'SRG-APP-000172-WSR-000104', 'title' => 'The web server must use cryptography to protect the integrity of remote sessions', 'description' => 'Data exchanged between the user and the web server can range from static display data to credentials used to log into the hosted application.', 'stig_version' => 'Web Server V2R3', 'stig_severity' => 'high'],
        ['framework' => 'STIG', 'code' => 'SRG-APP-000439-WSR-000151', 'title' => 'The web server must be tuned to handle the operational requirements of the hosted application', 'description' => 'A Denial of Service (DoS) can occur when the web server is so overwhelmed that it can no longer respond to additional requests.', 'stig_version' => 'Web Server V2R3', 'stig_severity' => 'medium'],
    ];

    $inserted = 0;
    foreach ($controls as $control) {
        // Check if control already exists
        $existing = $db->fetchOne(
            'SELECT id FROM controls WHERE framework = ? AND code = ?',
            [$control['framework'], $control['code']]
        );

        if (!$existing) {
            $db->insert('controls', $control);
            $inserted++;
        }
    }

    echo "Seeded $inserted STIG controls (skipped " . (count($controls) - $inserted) . " existing)\n";
};
