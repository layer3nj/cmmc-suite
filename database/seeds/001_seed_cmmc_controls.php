<?php

/**
 * Seed CMMC 2.0 Controls (ML1-ML3)
 * This is a representative sample - production would include all controls
 */

return function($db) {
    $controls = [
        // CMMC ML1 - Access Control
        ['framework' => 'CMMC', 'code' => 'AC.L1-3.1.1', 'title' => 'Limit information system access to authorized users', 'description' => 'Limit information system access to authorized users, processes acting on behalf of authorized users, or devices (including other information systems).', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'AC.L1-3.1.2', 'title' => 'Limit information system access to authorized transactions', 'description' => 'Limit information system access to the types of transactions and functions that authorized users are permitted to execute.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'AC.L1-3.1.20', 'title' => 'Verify and control/limit connections to external systems', 'description' => 'Verify and control/limit connections to and use of external information systems.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'AC.L1-3.1.22', 'title' => 'Control information posted or processed on publicly accessible systems', 'description' => 'Control information posted or processed on publicly accessible information systems.', 'ml_level' => 1],

        // CMMC ML1 - Identification and Authentication
        ['framework' => 'CMMC', 'code' => 'IA.L1-3.5.1', 'title' => 'Identify information system users', 'description' => 'Identify information system users, processes acting on behalf of users, or devices.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'IA.L1-3.5.2', 'title' => 'Authenticate users and processes', 'description' => 'Authenticate (or verify) the identities of those users, processes, or devices, as a prerequisite to allowing access to organizational information systems.', 'ml_level' => 1],

        // CMMC ML1 - Media Protection
        ['framework' => 'CMMC', 'code' => 'MP.L1-3.8.3', 'title' => 'Sanitize or destroy information system media', 'description' => 'Sanitize or destroy information system media containing Federal Contract Information before disposal or release for reuse.', 'ml_level' => 1],

        // CMMC ML1 - Physical Protection
        ['framework' => 'CMMC', 'code' => 'PE.L1-3.10.1', 'title' => 'Limit physical access', 'description' => 'Limit physical access to organizational information systems, equipment, and the respective operating environments to authorized individuals.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'PE.L1-3.10.3', 'title' => 'Escort visitors and monitor activity', 'description' => 'Escort visitors and monitor visitor activity.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'PE.L1-3.10.4', 'title' => 'Maintain audit logs of physical access', 'description' => 'Maintain audit logs of physical access.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'PE.L1-3.10.5', 'title' => 'Control and manage physical access devices', 'description' => 'Control and manage physical access devices.', 'ml_level' => 1],

        // CMMC ML1 - System and Communications Protection
        ['framework' => 'CMMC', 'code' => 'SC.L1-3.13.1', 'title' => 'Monitor and control communications at external boundaries', 'description' => 'Monitor and control communications at the external boundary of the system and at key internal boundaries within the system.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'SC.L1-3.13.5', 'title' => 'Implement subnetworks for publicly accessible components', 'description' => 'Implement subnetworks for publicly accessible system components that are physically or logically separated from internal networks.', 'ml_level' => 1],

        // CMMC ML1 - System and Information Integrity
        ['framework' => 'CMMC', 'code' => 'SI.L1-3.14.1', 'title' => 'Identify and manage information system flaws', 'description' => 'Identify, report, and correct information and information system flaws in a timely manner.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'SI.L1-3.14.2', 'title' => 'Provide protection from malicious code', 'description' => 'Provide protection from malicious code at appropriate locations within organizational information systems.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'SI.L1-3.14.4', 'title' => 'Update malicious code protection mechanisms', 'description' => 'Update malicious code protection mechanisms when new releases are available.', 'ml_level' => 1],
        ['framework' => 'CMMC', 'code' => 'SI.L1-3.14.5', 'title' => 'Perform periodic scans and real-time scans', 'description' => 'Perform periodic scans of the information system and real-time scans of files from external sources as files are downloaded, opened, or executed.', 'ml_level' => 1],

        // CMMC ML2 - Access Control
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.3', 'title' => 'Control the flow of CUI', 'description' => 'Control the flow of CUI in accordance with approved authorizations.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.4', 'title' => 'Separate duties of individuals', 'description' => 'Separate the duties of individuals to reduce the risk of malevolent activity without collusion.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.5', 'title' => 'Employ least privilege', 'description' => 'Employ the principle of least privilege, including for specific security functions and privileged accounts.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AC.L2-3.1.6', 'title' => 'Use non-privileged accounts', 'description' => 'Use non-privileged accounts or roles when accessing nonsecurity functions.', 'ml_level' => 2],

        // CMMC ML2 - Awareness and Training
        ['framework' => 'CMMC', 'code' => 'AT.L2-3.2.1', 'title' => 'Ensure managers and users are aware of security risks', 'description' => 'Ensure that managers and system administrators and users of organizational information systems are made aware of the security risks associated with their activities and of the applicable policies, standards, and procedures related to the security of those systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AT.L2-3.2.2', 'title' => 'Provide security awareness training', 'description' => 'Ensure that personnel are trained to carry out their assigned information security-related duties and responsibilities.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AT.L2-3.2.3', 'title' => 'Provide security awareness training on recognizing and reporting threats', 'description' => 'Provide security awareness training on recognizing and reporting potential indicators of insider threat.', 'ml_level' => 2],

        // CMMC ML2 - Audit and Accountability
        ['framework' => 'CMMC', 'code' => 'AU.L2-3.3.1', 'title' => 'Create and retain system audit logs', 'description' => 'Create, protect, and retain information system audit records to the extent needed to enable monitoring, analysis, investigation, and reporting of unlawful, unauthorized, or inappropriate information system activity.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'AU.L2-3.3.2', 'title' => 'Ensure actions can be traced to users', 'description' => 'Ensure that the actions of individual information system users can be uniquely traced to those users so they can be held accountable for their actions.', 'ml_level' => 2],

        // CMMC ML2 - Configuration Management
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.1', 'title' => 'Establish and maintain baseline configurations', 'description' => 'Establish and maintain baseline configurations and inventories of organizational information systems (including hardware, software, firmware, and documentation) throughout the respective system development life cycles.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CM.L2-3.4.2', 'title' => 'Establish and enforce security configuration settings', 'description' => 'Establish and enforce security configuration settings for information technology products employed in organizational information systems.', 'ml_level' => 2],

        // CMMC ML2 - Incident Response
        ['framework' => 'CMMC', 'code' => 'IR.L2-3.6.1', 'title' => 'Establish operational incident-handling capability', 'description' => 'Establish an operational incident-handling capability for organizational information systems that includes adequate preparation, detection, analysis, containment, recovery, and user response activities.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'IR.L2-3.6.2', 'title' => 'Track, document, and report incidents', 'description' => 'Track, document, and report incidents to appropriate officials and/or authorities both internal and external to the organization.', 'ml_level' => 2],

        // CMMC ML2 - Maintenance
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.1', 'title' => 'Perform maintenance on organizational systems', 'description' => 'Perform maintenance on organizational information systems.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'MA.L2-3.7.2', 'title' => 'Control maintenance tools and media', 'description' => 'Provide effective controls on the tools, techniques, mechanisms, and personnel used to conduct information system maintenance.', 'ml_level' => 2],

        // CMMC ML2 - Risk Assessment
        ['framework' => 'CMMC', 'code' => 'RA.L2-3.11.1', 'title' => 'Periodically assess risk', 'description' => 'Periodically assess the risk to organizational operations (including mission, functions, image, or reputation), organizational assets, and individuals, resulting from the operation of organizational information systems and the associated processing, storage, or transmission of CUI.', 'ml_level' => 2],

        // CMMC ML2 - Security Assessment
        ['framework' => 'CMMC', 'code' => 'CA.L2-3.12.1', 'title' => 'Periodically assess security controls', 'description' => 'Periodically assess the security controls in organizational information systems to determine if the controls are effective in their application.', 'ml_level' => 2],
        ['framework' => 'CMMC', 'code' => 'CA.L2-3.12.2', 'title' => 'Develop and implement action plans', 'description' => 'Develop and implement plans of action designed to correct deficiencies and reduce or eliminate vulnerabilities in organizational information systems.', 'ml_level' => 2],

        // CMMC ML3 - Advanced Controls
        ['framework' => 'CMMC', 'code' => 'AC.L3-3.1.7', 'title' => 'Prevent non-privileged users from executing privileged functions', 'description' => 'Prevent non-privileged users from executing privileged functions and capture the execution of such functions in audit logs.', 'ml_level' => 3],
        ['framework' => 'CMMC', 'code' => 'AC.L3-3.1.12', 'title' => 'Monitor and control remote access sessions', 'description' => 'Monitor and control remote access sessions.', 'ml_level' => 3],
        ['framework' => 'CMMC', 'code' => 'AC.L3-3.1.18', 'title' => 'Control connection of mobile devices', 'description' => 'Control connection of mobile devices.', 'ml_level' => 3],

        ['framework' => 'CMMC', 'code' => 'AU.L3-3.3.8', 'title' => 'Protect audit information and tools', 'description' => 'Protect audit information and audit logging tools from unauthorized access, modification, and deletion.', 'ml_level' => 3],
        ['framework' => 'CMMC', 'code' => 'AU.L3-3.3.9', 'title' => 'Limit management of audit functionality', 'description' => 'Limit management of audit logging functionality to a subset of privileged users.', 'ml_level' => 3],

        ['framework' => 'CMMC', 'code' => 'IA.L3-3.5.10', 'title' => 'Store and transmit only cryptographically-protected passwords', 'description' => 'Store and transmit only cryptographically-protected passwords.', 'ml_level' => 3],
        ['framework' => 'CMMC', 'code' => 'IA.L3-3.5.11', 'title' => 'Obscure feedback of authentication information', 'description' => 'Obscure feedback of authentication information.', 'ml_level' => 3],

        ['framework' => 'CMMC', 'code' => 'IR.L3-3.6.3', 'title' => 'Test incident response capability', 'description' => 'Test the organizational incident response capability.', 'ml_level' => 3],

        ['framework' => 'CMMC', 'code' => 'SC.L3-3.13.8', 'title' => 'Implement cryptographic mechanisms', 'description' => 'Implement cryptographic mechanisms to prevent unauthorized disclosure of CUI during transmission unless otherwise protected by alternative physical safeguards.', 'ml_level' => 3],
        ['framework' => 'CMMC', 'code' => 'SC.L3-3.13.11', 'title' => 'Employ FIPS-validated cryptography', 'description' => 'Employ FIPS-validated cryptography when used to protect the confidentiality of CUI.', 'ml_level' => 3],

        ['framework' => 'CMMC', 'code' => 'SI.L3-3.14.6', 'title' => 'Monitor information systems including inbound and outbound communications', 'description' => 'Monitor organizational information systems, including inbound and outbound communications traffic, to detect attacks and indicators of potential attacks.', 'ml_level' => 3],
        ['framework' => 'CMMC', 'code' => 'SI.L3-3.14.7', 'title' => 'Identify unauthorized use of systems', 'description' => 'Identify unauthorized use of organizational information systems.', 'ml_level' => 3],
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

    echo "Seeded $inserted CMMC controls (skipped " . (count($controls) - $inserted) . " existing)\n";
};
