<?php

/**
 * Seed NIST SP 800-171 Controls (110 practices)
 * This is a representative sample - production includes all 110
 */

return function($db) {
    $controls = [
        // 3.1 Access Control
        ['framework' => 'NIST800171', 'code' => '3.1.1', 'title' => 'Limit system access to authorized users', 'description' => 'Limit information system access to authorized users, processes acting on behalf of authorized users, or devices (including other information systems).'],
        ['framework' => 'NIST800171', 'code' => '3.1.2', 'title' => 'Limit system access to authorized transactions and functions', 'description' => 'Limit information system access to the types of transactions and functions that authorized users are permitted to execute.'],
        ['framework' => 'NIST800171', 'code' => '3.1.3', 'title' => 'Control the flow of CUI', 'description' => 'Control the flow of CUI in accordance with approved authorizations.'],
        ['framework' => 'NIST800171', 'code' => '3.1.4', 'title' => 'Separate duties', 'description' => 'Separate the duties of individuals to reduce the risk of malevolent activity without collusion.'],
        ['framework' => 'NIST800171', 'code' => '3.1.5', 'title' => 'Employ least privilege', 'description' => 'Employ the principle of least privilege, including for specific security functions and privileged accounts.'],
        ['framework' => 'NIST800171', 'code' => '3.1.6', 'title' => 'Use non-privileged accounts', 'description' => 'Use non-privileged accounts or roles when accessing nonsecurity functions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.7', 'title' => 'Prevent non-privileged users from executing privileged functions', 'description' => 'Prevent non-privileged users from executing privileged functions and audit the execution of such functions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.8', 'title' => 'Limit unsuccessful logon attempts', 'description' => 'Limit unsuccessful logon attempts.'],
        ['framework' => 'NIST800171', 'code' => '3.1.9', 'title' => 'Provide privacy and security notices', 'description' => 'Provide privacy and security notices consistent with applicable CUI rules.'],
        ['framework' => 'NIST800171', 'code' => '3.1.10', 'title' => 'Use session lock with pattern-hiding displays', 'description' => 'Use session lock with pattern-hiding displays to prevent access/viewing of data after period of inactivity.'],
        ['framework' => 'NIST800171', 'code' => '3.1.11', 'title' => 'Terminate user sessions', 'description' => 'Terminate (automatically) a user session after a defined condition.'],
        ['framework' => 'NIST800171', 'code' => '3.1.12', 'title' => 'Monitor and control remote access sessions', 'description' => 'Monitor and control remote access sessions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.13', 'title' => 'Employ cryptographic mechanisms', 'description' => 'Employ cryptographic mechanisms to protect confidentiality of remote access sessions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.14', 'title' => 'Route remote access via managed access control points', 'description' => 'Route remote access via managed access control points.'],
        ['framework' => 'NIST800171', 'code' => '3.1.15', 'title' => 'Authorize remote execution and access', 'description' => 'Authorize remote execution of privileged commands and remote access to security-relevant information.'],
        ['framework' => 'NIST800171', 'code' => '3.1.16', 'title' => 'Authorize wireless access', 'description' => 'Authorize wireless access prior to allowing such connections.'],
        ['framework' => 'NIST800171', 'code' => '3.1.17', 'title' => 'Protect wireless access using authentication and encryption', 'description' => 'Protect wireless access using authentication and encryption.'],
        ['framework' => 'NIST800171', 'code' => '3.1.18', 'title' => 'Control connection of mobile devices', 'description' => 'Control connection of mobile devices.'],
        ['framework' => 'NIST800171', 'code' => '3.1.19', 'title' => 'Encrypt CUI on mobile devices', 'description' => 'Encrypt CUI on mobile devices and mobile computing platforms.'],
        ['framework' => 'NIST800171', 'code' => '3.1.20', 'title' => 'Verify and control/limit connections to external systems', 'description' => 'Verify and control/limit connections to and use of external information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.1.21', 'title' => 'Limit use of portable storage devices', 'description' => 'Limit use of organizational portable storage devices on external information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.1.22', 'title' => 'Control CUI posted or processed on publicly accessible systems', 'description' => 'Control CUI posted or processed on publicly accessible information systems.'],

        // 3.2 Awareness and Training
        ['framework' => 'NIST800171', 'code' => '3.2.1', 'title' => 'Ensure personnel are aware of security risks', 'description' => 'Ensure that managers, systems administrators, and users of organizational information systems are made aware of the security risks associated with their activities and of the applicable policies, standards, and procedures related to the security of organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.2.2', 'title' => 'Ensure personnel are trained', 'description' => 'Ensure that organizational personnel are adequately trained to carry out their assigned information security-related duties and responsibilities.'],
        ['framework' => 'NIST800171', 'code' => '3.2.3', 'title' => 'Provide security awareness training on insider threats', 'description' => 'Provide security awareness training on recognizing and reporting potential indicators of insider threat.'],

        // 3.3 Audit and Accountability
        ['framework' => 'NIST800171', 'code' => '3.3.1', 'title' => 'Create and retain audit logs and records', 'description' => 'Create, protect, and retain information system audit records to the extent needed to enable monitoring, analysis, investigation, and reporting of unlawful, unauthorized, or inappropriate information system activity.'],
        ['framework' => 'NIST800171', 'code' => '3.3.2', 'title' => 'Ensure actions can be traced to individual users', 'description' => 'Ensure that the actions of individual information system users can be uniquely traced to those users so they can be held accountable for their actions.'],
        ['framework' => 'NIST800171', 'code' => '3.3.3', 'title' => 'Review and update logged events', 'description' => 'Review and update logged events.'],
        ['framework' => 'NIST800171', 'code' => '3.3.4', 'title' => 'Alert in case of an audit logging process failure', 'description' => 'Alert in the event of an audit logging process failure.'],
        ['framework' => 'NIST800171', 'code' => '3.3.5', 'title' => 'Correlate audit records', 'description' => 'Correlate audit record review, analysis, and reporting processes for investigation and response to indications of inappropriate, suspicious, or unusual activity.'],
        ['framework' => 'NIST800171', 'code' => '3.3.6', 'title' => 'Provide audit reduction and report generation', 'description' => 'Provide audit reduction and report generation to support on-demand analysis and reporting.'],
        ['framework' => 'NIST800171', 'code' => '3.3.7', 'title' => 'Provide a system capability for automated log review', 'description' => 'Provide a system capability that compares and synchronizes internal system clocks with an authoritative source to generate time stamps for audit records.'],
        ['framework' => 'NIST800171', 'code' => '3.3.8', 'title' => 'Protect audit information and tools', 'description' => 'Protect audit information and audit logging tools from unauthorized access, modification, and deletion.'],
        ['framework' => 'NIST800171', 'code' => '3.3.9', 'title' => 'Limit management of audit logging', 'description' => 'Limit management of audit logging functionality to a subset of privileged users.'],

        // 3.4 Configuration Management
        ['framework' => 'NIST800171', 'code' => '3.4.1', 'title' => 'Establish and maintain baseline configurations', 'description' => 'Establish and maintain baseline configurations and inventories of organizational information systems (including hardware, software, firmware, and documentation) throughout the respective system development life cycles.'],
        ['framework' => 'NIST800171', 'code' => '3.4.2', 'title' => 'Establish and enforce security configuration settings', 'description' => 'Establish and enforce security configuration settings for information technology products employed in organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.4.3', 'title' => 'Track and document configuration changes', 'description' => 'Track, review, approve/disapprove, and audit changes to organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.4.4', 'title' => 'Analyze security impact of changes', 'description' => 'Analyze the security impact of changes prior to implementation.'],
        ['framework' => 'NIST800171', 'code' => '3.4.5', 'title' => 'Define and document security functions', 'description' => 'Define, document, approve, and enforce physical and logical access restrictions associated with changes to organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.4.6', 'title' => 'Employ least functionality', 'description' => 'Employ the principle of least functionality by configuring organizational information systems to provide only essential capabilities.'],
        ['framework' => 'NIST800171', 'code' => '3.4.7', 'title' => 'Restrict, disable, or prevent software execution', 'description' => 'Restrict, disable, or prevent the use of nonessential programs, functions, ports, protocols, and services.'],
        ['framework' => 'NIST800171', 'code' => '3.4.8', 'title' => 'Apply deny-by-exception policy', 'description' => 'Apply deny-by-exception (blacklist) policy to prevent the use of unauthorized software or deny-all, permit-by-exception (whitelisting) policy to allow the execution of authorized software.'],
        ['framework' => 'NIST800171', 'code' => '3.4.9', 'title' => 'Control and monitor user-installed software', 'description' => 'Control and monitor user-installed software.'],

        // 3.5 Identification and Authentication
        ['framework' => 'NIST800171', 'code' => '3.5.1', 'title' => 'Identify system users and processes', 'description' => 'Identify information system users, processes acting on behalf of users, or devices.'],
        ['framework' => 'NIST800171', 'code' => '3.5.2', 'title' => 'Authenticate users and processes', 'description' => 'Authenticate (or verify) the identities of those users, processes, or devices, as a prerequisite to allowing access to organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.5.3', 'title' => 'Use multifactor authentication', 'description' => 'Use multifactor authentication for local and network access to privileged accounts and for network access to non-privileged accounts.'],
        ['framework' => 'NIST800171', 'code' => '3.5.4', 'title' => 'Employ replay-resistant authentication mechanisms', 'description' => 'Employ replay-resistant authentication mechanisms for network access to privileged and non-privileged accounts.'],
        ['framework' => 'NIST800171', 'code' => '3.5.5', 'title' => 'Prevent reuse of identifiers', 'description' => 'Prevent reuse of identifiers for a defined period.'],
        ['framework' => 'NIST800171', 'code' => '3.5.6', 'title' => 'Disable identifiers after inactivity', 'description' => 'Disable identifiers after a defined period of inactivity.'],
        ['framework' => 'NIST800171', 'code' => '3.5.7', 'title' => 'Enforce minimum password complexity', 'description' => 'Enforce a minimum password complexity and change of characters when new passwords are created.'],
        ['framework' => 'NIST800171', 'code' => '3.5.8', 'title' => 'Prohibit password reuse', 'description' => 'Prohibit password reuse for a specified number of generations.'],
        ['framework' => 'NIST800171', 'code' => '3.5.9', 'title' => 'Allow temporary password use for system logons', 'description' => 'Allow temporary password use for system logons with an immediate change to a permanent password.'],
        ['framework' => 'NIST800171', 'code' => '3.5.10', 'title' => 'Store and transmit only cryptographically-protected passwords', 'description' => 'Store and transmit only cryptographically-protected passwords.'],
        ['framework' => 'NIST800171', 'code' => '3.5.11', 'title' => 'Obscure feedback of authentication information', 'description' => 'Obscure feedback of authentication information.'],

        // Additional families would continue (3.6-3.14)...
        // For brevity, adding key controls from remaining families

        // 3.6 Incident Response
        ['framework' => 'NIST800171', 'code' => '3.6.1', 'title' => 'Establish incident-handling capability', 'description' => 'Establish an operational incident-handling capability for organizational information systems that includes adequate preparation, detection, analysis, containment, recovery, and user response activities.'],
        ['framework' => 'NIST800171', 'code' => '3.6.2', 'title' => 'Track, document, and report incidents', 'description' => 'Track, document, and report incidents to appropriate organizational officials and/or authorities.'],
        ['framework' => 'NIST800171', 'code' => '3.6.3', 'title' => 'Test incident response capability', 'description' => 'Test the organizational incident response capability.'],

        // 3.13 System and Communications Protection
        ['framework' => 'NIST800171', 'code' => '3.13.1', 'title' => 'Monitor and control communications at system boundaries', 'description' => 'Monitor and control communications at the external boundary of the information system and at key internal boundaries within the system.'],
        ['framework' => 'NIST800171', 'code' => '3.13.2', 'title' => 'Employ architectural designs and configurations', 'description' => 'Employ architectural designs, software development techniques, and systems engineering principles that promote effective information security within organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.13.5', 'title' => 'Implement subnetworks', 'description' => 'Implement subnetworks for publicly accessible system components that are physically or logically separated from internal networks.'],
        ['framework' => 'NIST800171', 'code' => '3.13.8', 'title' => 'Implement cryptographic mechanisms for transmission', 'description' => 'Implement cryptographic mechanisms to prevent unauthorized disclosure of CUI during transmission unless otherwise protected by alternative physical safeguards.'],
        ['framework' => 'NIST800171', 'code' => '3.13.11', 'title' => 'Employ FIPS-validated cryptography', 'description' => 'Employ FIPS-validated cryptography when used to protect the confidentiality of CUI.'],
        ['framework' => 'NIST800171', 'code' => '3.13.16', 'title' => 'Protect the confidentiality of CUI at rest', 'description' => 'Protect the confidentiality of CUI at rest.'],

        // 3.14 System and Information Integrity
        ['framework' => 'NIST800171', 'code' => '3.14.1', 'title' => 'Identify and manage information system flaws', 'description' => 'Identify, report, and correct information and information system flaws in a timely manner.'],
        ['framework' => 'NIST800171', 'code' => '3.14.2', 'title' => 'Provide protection from malicious code', 'description' => 'Provide protection from malicious code at appropriate locations within organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.14.3', 'title' => 'Monitor system security alerts and advisories', 'description' => 'Monitor information system security alerts and advisories and take appropriate action in response.'],
        ['framework' => 'NIST800171', 'code' => '3.14.4', 'title' => 'Update malicious code protection mechanisms', 'description' => 'Update malicious code protection mechanisms when new releases are available.'],
        ['framework' => 'NIST800171', 'code' => '3.14.5', 'title' => 'Perform periodic scans and real-time scans', 'description' => 'Perform periodic scans of the information system and real-time scans of files from external sources as files are downloaded, opened, or executed.'],
        ['framework' => 'NIST800171', 'code' => '3.14.6', 'title' => 'Monitor the information system', 'description' => 'Monitor organizational information systems, including inbound and outbound communications traffic, to detect attacks and indicators of potential attacks.'],
        ['framework' => 'NIST800171', 'code' => '3.14.7', 'title' => 'Identify unauthorized use of the information system', 'description' => 'Identify unauthorized use of organizational information systems.'],
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

    // Return count for logging (don't echo - breaks JSON response)
};
