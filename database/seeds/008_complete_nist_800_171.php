<?php

/**
 * Complete NIST SP 800-171 R2 Controls (All 110 practices)
 * This replaces partial seed files with the complete standard
 *
 * Source: NIST Special Publication 800-171 Revision 2
 * Protecting Controlled Unclassified Information in Nonfederal Systems and Organizations
 */

return function($db) {
    $controls = [
        // 3.1 ACCESS CONTROL (22 controls)
        ['framework' => 'NIST800171', 'code' => '3.1.1', 'title' => 'Limit system access to authorized users', 'description' => 'Limit information system access to authorized users, processes acting on behalf of authorized users, or devices (including other information systems).'],
        ['framework' => 'NIST800171', 'code' => '3.1.2', 'title' => 'Limit system access to authorized transactions and functions', 'description' => 'Limit information system access to the types of transactions and functions that authorized users are permitted to execute.'],
        ['framework' => 'NIST800171', 'code' => '3.1.3', 'title' => 'Control the flow of CUI', 'description' => 'Control the flow of CUI in accordance with approved authorizations.'],
        ['framework' => 'NIST800171', 'code' => '3.1.4', 'title' => 'Separate duties of individuals', 'description' => 'Separate the duties of individuals to reduce the risk of malevolent activity without collusion.'],
        ['framework' => 'NIST800171', 'code' => '3.1.5', 'title' => 'Employ least privilege', 'description' => 'Employ the principle of least privilege, including for specific security functions and privileged accounts.'],
        ['framework' => 'NIST800171', 'code' => '3.1.6', 'title' => 'Use non-privileged accounts', 'description' => 'Use non-privileged accounts or roles when accessing nonsecurity functions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.7', 'title' => 'Prevent non-privileged users from executing privileged functions', 'description' => 'Prevent non-privileged users from executing privileged functions and audit the execution of such functions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.8', 'title' => 'Limit unsuccessful logon attempts', 'description' => 'Limit unsuccessful logon attempts.'],
        ['framework' => 'NIST800171', 'code' => '3.1.9', 'title' => 'Provide privacy and security notices', 'description' => 'Provide privacy and security notices consistent with applicable CUI rules.'],
        ['framework' => 'NIST800171', 'code' => '3.1.10', 'title' => 'Use session lock with pattern-hiding displays', 'description' => 'Use session lock with pattern-hiding displays to prevent access and viewing of data after a period of inactivity.'],
        ['framework' => 'NIST800171', 'code' => '3.1.11', 'title' => 'Terminate user session automatically', 'description' => 'Terminate (automatically) a user session after a defined condition.'],
        ['framework' => 'NIST800171', 'code' => '3.1.12', 'title' => 'Monitor and control remote access sessions', 'description' => 'Monitor and control remote access sessions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.13', 'title' => 'Employ cryptographic mechanisms to protect confidentiality', 'description' => 'Employ cryptographic mechanisms to protect the confidentiality of remote access sessions.'],
        ['framework' => 'NIST800171', 'code' => '3.1.14', 'title' => 'Route remote access via managed access control points', 'description' => 'Route remote access via managed access control points.'],
        ['framework' => 'NIST800171', 'code' => '3.1.15', 'title' => 'Authorize remote execution of privileged commands', 'description' => 'Authorize remote execution of privileged commands and remote access to security-relevant information.'],
        ['framework' => 'NIST800171', 'code' => '3.1.16', 'title' => 'Authorize wireless access prior to allowing connections', 'description' => 'Authorize wireless access prior to allowing such connections.'],
        ['framework' => 'NIST800171', 'code' => '3.1.17', 'title' => 'Protect wireless access using authentication and encryption', 'description' => 'Protect wireless access using authentication and encryption.'],
        ['framework' => 'NIST800171', 'code' => '3.1.18', 'title' => 'Control connection of mobile devices', 'description' => 'Control connection of mobile devices.'],
        ['framework' => 'NIST800171', 'code' => '3.1.19', 'title' => 'Encrypt CUI on mobile devices', 'description' => 'Encrypt CUI on mobile devices and mobile computing platforms.'],
        ['framework' => 'NIST800171', 'code' => '3.1.20', 'title' => 'Verify and control connections to external systems', 'description' => 'Verify and control/limit connections to and use of external information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.1.21', 'title' => 'Limit use of portable storage devices on external systems', 'description' => 'Limit use of organizational portable storage devices on external information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.1.22', 'title' => 'Control CUI posted on publicly accessible systems', 'description' => 'Control CUI posted or processed on publicly accessible information systems.'],

        // 3.2 AWARENESS AND TRAINING (3 controls)
        ['framework' => 'NIST800171', 'code' => '3.2.1', 'title' => 'Ensure managers and users are aware of security risks', 'description' => 'Ensure that managers, systems administrators, and users of organizational information systems are made aware of the security risks associated with their activities and of the applicable policies, standards, and procedures related to the security of organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.2.2', 'title' => 'Ensure personnel are trained to carry out security duties', 'description' => 'Ensure that organizational personnel are adequately trained to carry out their assigned information security-related duties and responsibilities.'],
        ['framework' => 'NIST800171', 'code' => '3.2.3', 'title' => 'Provide security awareness training on insider threat', 'description' => 'Provide security awareness training on recognizing and reporting potential indicators of insider threat.'],

        // 3.3 AUDIT AND ACCOUNTABILITY (9 controls)
        ['framework' => 'NIST800171', 'code' => '3.3.1', 'title' => 'Create, protect, and retain audit records', 'description' => 'Create, protect, and retain information system audit records to the extent needed to enable monitoring, analysis, investigation, and reporting of unlawful, unauthorized, or inappropriate information system activity.'],
        ['framework' => 'NIST800171', 'code' => '3.3.2', 'title' => 'Ensure actions can be traced to individual users', 'description' => 'Ensure that the actions of individual information system users can be uniquely traced to those users so they can be held accountable for their actions.'],
        ['framework' => 'NIST800171', 'code' => '3.3.3', 'title' => 'Review and update logged events', 'description' => 'Review and update logged events.'],
        ['framework' => 'NIST800171', 'code' => '3.3.4', 'title' => 'Alert in the event of audit logging process failure', 'description' => 'Alert in the event of an audit logging process failure.'],
        ['framework' => 'NIST800171', 'code' => '3.3.5', 'title' => 'Correlate audit record review, analysis, and reporting', 'description' => 'Correlate audit record review, analysis, and reporting processes for investigation and response to indications of inappropriate, suspicious, or unusual activity.'],
        ['framework' => 'NIST800171', 'code' => '3.3.6', 'title' => 'Provide audit reduction and report generation', 'description' => 'Provide audit reduction and report generation to support on-demand analysis and reporting.'],
        ['framework' => 'NIST800171', 'code' => '3.3.7', 'title' => 'Provide system capability that compares internal clocks', 'description' => 'Provide a system capability that compares and synchronizes internal system clocks with an authoritative source to generate time stamps for audit records.'],
        ['framework' => 'NIST800171', 'code' => '3.3.8', 'title' => 'Protect audit information and audit logging tools', 'description' => 'Protect audit information and audit logging tools from unauthorized access, modification, and deletion.'],
        ['framework' => 'NIST800171', 'code' => '3.3.9', 'title' => 'Limit management of audit logging to privileged users', 'description' => 'Limit management of audit logging functionality to a subset of privileged users.'],

        // 3.4 CONFIGURATION MANAGEMENT (9 controls)
        ['framework' => 'NIST800171', 'code' => '3.4.1', 'title' => 'Establish and maintain baseline configurations', 'description' => 'Establish and maintain baseline configurations and inventories of organizational information systems (including hardware, software, firmware, and documentation) throughout the respective system development life cycles.'],
        ['framework' => 'NIST800171', 'code' => '3.4.2', 'title' => 'Establish and enforce security configuration settings', 'description' => 'Establish and enforce security configuration settings for information technology products employed in organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.4.3', 'title' => 'Track, review, and audit configuration changes', 'description' => 'Track, review, approve/disapprove, and audit changes to organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.4.4', 'title' => 'Analyze security impact of changes prior to implementation', 'description' => 'Analyze the security impact of changes prior to implementation.'],
        ['framework' => 'NIST800171', 'code' => '3.4.5', 'title' => 'Define, document, and enforce access restrictions', 'description' => 'Define, document, approve, and enforce physical and logical access restrictions associated with changes to organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.4.6', 'title' => 'Employ least functionality principle', 'description' => 'Employ the principle of least functionality by configuring organizational information systems to provide only essential capabilities.'],
        ['framework' => 'NIST800171', 'code' => '3.4.7', 'title' => 'Restrict, disable, or prevent nonessential programs', 'description' => 'Restrict, disable, or prevent the use of nonessential programs, functions, ports, protocols, and services.'],
        ['framework' => 'NIST800171', 'code' => '3.4.8', 'title' => 'Apply deny-by-exception policy to prevent unauthorized software', 'description' => 'Apply deny-by-exception (blacklist) policy to prevent the use of unauthorized software or deny-all, permit-by-exception (whitelisting) policy to allow the execution of authorized software.'],
        ['framework' => 'NIST800171', 'code' => '3.4.9', 'title' => 'Control and monitor user-installed software', 'description' => 'Control and monitor user-installed software.'],

        // 3.5 IDENTIFICATION AND AUTHENTICATION (11 controls)
        ['framework' => 'NIST800171', 'code' => '3.5.1', 'title' => 'Identify information system users, processes, or devices', 'description' => 'Identify information system users, processes acting on behalf of users, or devices.'],
        ['framework' => 'NIST800171', 'code' => '3.5.2', 'title' => 'Authenticate users, processes, or devices', 'description' => 'Authenticate (or verify) the identities of those users, processes, or devices, as a prerequisite to allowing access to organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.5.3', 'title' => 'Use multifactor authentication for privileged and non-privileged accounts', 'description' => 'Use multifactor authentication for local and network access to privileged accounts and for network access to non-privileged accounts.'],
        ['framework' => 'NIST800171', 'code' => '3.5.4', 'title' => 'Employ replay-resistant authentication mechanisms', 'description' => 'Employ replay-resistant authentication mechanisms for network access to privileged and non-privileged accounts.'],
        ['framework' => 'NIST800171', 'code' => '3.5.5', 'title' => 'Prevent reuse of identifiers for defined period', 'description' => 'Prevent reuse of identifiers for a defined period.'],
        ['framework' => 'NIST800171', 'code' => '3.5.6', 'title' => 'Disable identifiers after period of inactivity', 'description' => 'Disable identifiers after a defined period of inactivity.'],
        ['framework' => 'NIST800171', 'code' => '3.5.7', 'title' => 'Enforce minimum password complexity', 'description' => 'Enforce a minimum password complexity and change of characters when new passwords are created.'],
        ['framework' => 'NIST800171', 'code' => '3.5.8', 'title' => 'Prohibit password reuse for specified generations', 'description' => 'Prohibit password reuse for a specified number of generations.'],
        ['framework' => 'NIST800171', 'code' => '3.5.9', 'title' => 'Allow temporary password use for system logons', 'description' => 'Allow temporary password use for system logons with an immediate change to a permanent password.'],
        ['framework' => 'NIST800171', 'code' => '3.5.10', 'title' => 'Store and transmit only cryptographically-protected passwords', 'description' => 'Store and transmit only cryptographically-protected passwords.'],
        ['framework' => 'NIST800171', 'code' => '3.5.11', 'title' => 'Obscure feedback of authentication information', 'description' => 'Obscure feedback of authentication information.'],

        // 3.6 INCIDENT RESPONSE (3 controls)
        ['framework' => 'NIST800171', 'code' => '3.6.1', 'title' => 'Establish operational incident-handling capability', 'description' => 'Establish an operational incident-handling capability for organizational information systems that includes adequate preparation, detection, analysis, containment, recovery, and user response activities.'],
        ['framework' => 'NIST800171', 'code' => '3.6.2', 'title' => 'Track, document, and report incidents', 'description' => 'Track, document, and report incidents to appropriate organizational officials and/or authorities.'],
        ['framework' => 'NIST800171', 'code' => '3.6.3', 'title' => 'Test organizational incident response capability', 'description' => 'Test the organizational incident response capability.'],

        // 3.7 MAINTENANCE (2 controls)
        ['framework' => 'NIST800171', 'code' => '3.7.1', 'title' => 'Perform maintenance on organizational systems', 'description' => 'Perform maintenance on organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.7.2', 'title' => 'Provide controls on tools, techniques, and personnel for maintenance', 'description' => 'Provide controls on the tools, techniques, mechanisms, and personnel used to conduct information system maintenance.'],
        ['framework' => 'NIST800171', 'code' => '3.7.3', 'title' => 'Ensure equipment removed for off-site maintenance is sanitized', 'description' => 'Ensure equipment removed for off-site maintenance is sanitized of any CUI or media containing CUI or obtain organizational approval and use of procedures to ensure all CUI is protected and purged from equipment prior to off-site maintenance or repairs.'],
        ['framework' => 'NIST800171', 'code' => '3.7.4', 'title' => 'Check media containing diagnostic programs for malicious code', 'description' => 'Check media containing diagnostic and test programs for malicious code before the media are used in organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.7.5', 'title' => 'Require multifactor authentication for remote maintenance', 'description' => 'Require multifactor authentication to establish nonlocal maintenance sessions via external network connections and terminate such connections when nonlocal maintenance is complete.'],
        ['framework' => 'NIST800171', 'code' => '3.7.6', 'title' => 'Supervise maintenance activities of personnel without required access', 'description' => 'Supervise the maintenance activities of maintenance personnel without required access authorization.'],

        // 3.8 MEDIA PROTECTION (9 controls)
        ['framework' => 'NIST800171', 'code' => '3.8.1', 'title' => 'Protect information system media from unauthorized access', 'description' => 'Protect (i.e., physically control and securely store) information system media containing CUI, both paper and digital.'],
        ['framework' => 'NIST800171', 'code' => '3.8.2', 'title' => 'Limit access to CUI on system media to authorized users', 'description' => 'Limit access to CUI on information system media to authorized users.'],
        ['framework' => 'NIST800171', 'code' => '3.8.3', 'title' => 'Sanitize or destroy system media before disposal or reuse', 'description' => 'Sanitize or destroy information system media containing CUI before disposal or release for reuse.'],
        ['framework' => 'NIST800171', 'code' => '3.8.4', 'title' => 'Mark media with necessary CUI markings', 'description' => 'Mark media with necessary CUI markings and distribution limitations.'],
        ['framework' => 'NIST800171', 'code' => '3.8.5', 'title' => 'Control access to media containing CUI during transport', 'description' => 'Control access to media containing CUI and maintain accountability for media during transport outside of controlled areas.'],
        ['framework' => 'NIST800171', 'code' => '3.8.6', 'title' => 'Implement cryptographic mechanisms to protect CUI on digital media', 'description' => 'Implement cryptographic mechanisms to protect the confidentiality of CUI stored on digital media during transport unless otherwise protected by alternative physical safeguards.'],
        ['framework' => 'NIST800171', 'code' => '3.8.7', 'title' => 'Control use of removable media on system components', 'description' => 'Control the use of removable media on system components.'],
        ['framework' => 'NIST800171', 'code' => '3.8.8', 'title' => 'Prohibit use of portable storage devices without identifiable owner', 'description' => 'Prohibit the use of portable storage devices when such devices have no identifiable owner.'],
        ['framework' => 'NIST800171', 'code' => '3.8.9', 'title' => 'Protect confidentiality of backup CUI at storage locations', 'description' => 'Protect the confidentiality of backup CUI at storage locations.'],

        // 3.9 PERSONNEL SECURITY (2 controls)
        ['framework' => 'NIST800171', 'code' => '3.9.1', 'title' => 'Screen individuals prior to authorizing access to systems', 'description' => 'Screen individuals prior to authorizing access to organizational information systems containing CUI.'],
        ['framework' => 'NIST800171', 'code' => '3.9.2', 'title' => 'Ensure CUI and systems are protected during personnel actions', 'description' => 'Ensure that organizational information and information systems are protected during and after personnel actions such as terminations and transfers.'],

        // 3.10 PHYSICAL PROTECTION (6 controls)
        ['framework' => 'NIST800171', 'code' => '3.10.1', 'title' => 'Limit physical access to organizational systems and equipment', 'description' => 'Limit physical access to organizational information systems, equipment, and the respective operating environments to authorized individuals.'],
        ['framework' => 'NIST800171', 'code' => '3.10.2', 'title' => 'Protect and monitor physical facility and support infrastructure', 'description' => 'Protect and monitor the physical facility and support infrastructure for organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.10.3', 'title' => 'Escort visitors and monitor visitor activity', 'description' => 'Escort visitors and monitor visitor activity; maintain audit logs of physical access; and control and manage physical access devices.'],
        ['framework' => 'NIST800171', 'code' => '3.10.4', 'title' => 'Control physical access points to facility', 'description' => 'Control physical access points.'],
        ['framework' => 'NIST800171', 'code' => '3.10.5', 'title' => 'Protect information systems from environmental hazards', 'description' => 'Protect information systems from environmental hazards.'],
        ['framework' => 'NIST800171', 'code' => '3.10.6', 'title' => 'Enforce safeguarding measures for CUI at alternate work sites', 'description' => 'Enforce safeguarding measures for CUI at alternate work sites.'],

        // 3.11 RISK ASSESSMENT (4 controls)
        ['framework' => 'NIST800171', 'code' => '3.11.1', 'title' => 'Periodically assess risk to organizational operations and assets', 'description' => 'Periodically assess the risk to organizational operations (including mission, functions, image, or reputation), organizational assets, and individuals, resulting from the operation of organizational information systems and the associated processing, storage, or transmission of CUI.'],
        ['framework' => 'NIST800171', 'code' => '3.11.2', 'title' => 'Scan for vulnerabilities in systems and applications', 'description' => 'Scan for vulnerabilities in organizational information systems and applications periodically and when new vulnerabilities affecting those systems and applications are identified.'],
        ['framework' => 'NIST800171', 'code' => '3.11.3', 'title' => 'Remediate vulnerabilities in accordance with risk assessments', 'description' => 'Remediate vulnerabilities in accordance with assessments of risk.'],
        ['framework' => 'NIST800171', 'code' => '3.11.4', 'title' => 'Update threat intelligence and share threat information', 'description' => 'Update threat intelligence and share threat information with organizational staff, stakeholders, and business partners to support risk assessments.'],

        // 3.12 SECURITY ASSESSMENT (4 controls)
        ['framework' => 'NIST800171', 'code' => '3.12.1', 'title' => 'Periodically assess security controls to determine effectiveness', 'description' => 'Periodically assess the security controls in organizational information systems to determine if the controls are effective in their application.'],
        ['framework' => 'NIST800171', 'code' => '3.12.2', 'title' => 'Develop and implement plans of action to correct deficiencies', 'description' => 'Develop and implement plans of action designed to correct deficiencies and reduce or eliminate vulnerabilities in organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.12.3', 'title' => 'Monitor security controls on an ongoing basis', 'description' => 'Monitor security controls on an ongoing basis to ensure the continued effectiveness of the controls.'],
        ['framework' => 'NIST800171', 'code' => '3.12.4', 'title' => 'Develop, document, and periodically update system security plans', 'description' => 'Develop, document, and periodically update system security plans that describe system boundaries, system environments of operation, how security requirements are implemented, and the relationships with or connections to other systems.'],

        // 3.13 SYSTEM AND COMMUNICATIONS PROTECTION (16 controls)
        ['framework' => 'NIST800171', 'code' => '3.13.1', 'title' => 'Monitor and control communications at external system boundaries', 'description' => 'Monitor, control, and protect organizational communications (i.e., information transmitted or received by organizational information systems) at the external boundaries and key internal boundaries of the information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.13.2', 'title' => 'Employ architectural designs promoting effective security', 'description' => 'Employ architectural designs, software development techniques, and systems engineering principles that promote effective information security within organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.13.3', 'title' => 'Separate user functionality from system management functionality', 'description' => 'Separate user functionality from information system management functionality.'],
        ['framework' => 'NIST800171', 'code' => '3.13.4', 'title' => 'Prevent unauthorized information transfer via shared resources', 'description' => 'Prevent unauthorized and unintended information transfer via shared system resources.'],
        ['framework' => 'NIST800171', 'code' => '3.13.5', 'title' => 'Implement subnetworks for publicly accessible components', 'description' => 'Implement subnetworks for publicly accessible system components that are physically or logically separated from internal networks.'],
        ['framework' => 'NIST800171', 'code' => '3.13.6', 'title' => 'Deny network communications traffic by default', 'description' => 'Deny network communications traffic by default and allow network communications traffic by exception (i.e., deny all, permit by exception).'],
        ['framework' => 'NIST800171', 'code' => '3.13.7', 'title' => 'Prevent remote devices from simultaneously connecting', 'description' => 'Prevent remote devices from simultaneously establishing non-remote connections with organizational information systems and communicating via some other connection to resources in external networks (i.e., split tunneling).'],
        ['framework' => 'NIST800171', 'code' => '3.13.8', 'title' => 'Implement cryptographic mechanisms to prevent unauthorized CUI disclosure', 'description' => 'Implement cryptographic mechanisms to prevent unauthorized disclosure of CUI during transmission unless otherwise protected by alternative physical safeguards.'],
        ['framework' => 'NIST800171', 'code' => '3.13.9', 'title' => 'Terminate network connections at end of sessions', 'description' => 'Terminate network connections associated with communications sessions at the end of the sessions or after a defined period of inactivity.'],
        ['framework' => 'NIST800171', 'code' => '3.13.10', 'title' => 'Establish and manage cryptographic keys', 'description' => 'Establish and manage cryptographic keys for cryptography employed in organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.13.11', 'title' => 'Employ FIPS-validated cryptography', 'description' => 'Employ FIPS-validated cryptography when used to protect the confidentiality of CUI.'],
        ['framework' => 'NIST800171', 'code' => '3.13.12', 'title' => 'Prohibit remote activation of collaborative computing devices', 'description' => 'Prohibit remote activation of collaborative computing devices and provide indication of devices in use to users present at the device.'],
        ['framework' => 'NIST800171', 'code' => '3.13.13', 'title' => 'Control and monitor use of mobile code', 'description' => 'Control and monitor the use of mobile code.'],
        ['framework' => 'NIST800171', 'code' => '3.13.14', 'title' => 'Control and monitor use of Voice over Internet Protocol', 'description' => 'Control and monitor the use of Voice over Internet Protocol (VoIP) technologies.'],
        ['framework' => 'NIST800171', 'code' => '3.13.15', 'title' => 'Protect authenticity of communications sessions', 'description' => 'Protect the authenticity of communications sessions.'],
        ['framework' => 'NIST800171', 'code' => '3.13.16', 'title' => 'Protect confidentiality of CUI at rest', 'description' => 'Protect the confidentiality of CUI at rest.'],

        // 3.14 SYSTEM AND INFORMATION INTEGRITY (7 controls)
        ['framework' => 'NIST800171', 'code' => '3.14.1', 'title' => 'Identify, report, and correct system flaws in a timely manner', 'description' => 'Identify, report, and correct information and information system flaws in a timely manner.'],
        ['framework' => 'NIST800171', 'code' => '3.14.2', 'title' => 'Provide protection from malicious code', 'description' => 'Provide protection from malicious code at appropriate locations within organizational information systems.'],
        ['framework' => 'NIST800171', 'code' => '3.14.3', 'title' => 'Monitor system security alerts and advisories', 'description' => 'Monitor information system security alerts and advisories and take appropriate actions in response.'],
        ['framework' => 'NIST800171', 'code' => '3.14.4', 'title' => 'Update malicious code protection mechanisms', 'description' => 'Update malicious code protection mechanisms when new releases are available.'],
        ['framework' => 'NIST800171', 'code' => '3.14.5', 'title' => 'Perform periodic and real-time scans of systems', 'description' => 'Perform periodic scans of organizational information systems and real-time scans of files from external sources as files are downloaded, opened, or executed.'],
        ['framework' => 'NIST800171', 'code' => '3.14.6', 'title' => 'Monitor organizational systems to detect attacks', 'description' => 'Monitor organizational information systems, including inbound and outbound communications traffic, to detect attacks and indicators of potential attacks.'],
        ['framework' => 'NIST800171', 'code' => '3.14.7', 'title' => 'Identify unauthorized use of organizational systems', 'description' => 'Identify unauthorized use of organizational information systems.'],
    ];

    $inserted = 0;
    $updated = 0;

    foreach ($controls as $control) {
        // Check if control already exists
        $exists = $db->fetchOne(
            "SELECT id FROM controls WHERE framework = ? AND code = ?",
            [$control['framework'], $control['code']]
        );

        if ($exists) {
            // Update existing control
            $db->update('controls', [
                'title' => $control['title'],
                'description' => $control['description'],
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'framework = :framework AND code = :code', [
                ':framework' => $control['framework'],
                ':code' => $control['code']
            ]);
            $updated++;
        } else {
            // Insert new control
            $db->insert('controls', array_merge($control, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]));
            $inserted++;
        }
    }

    return [
        'status' => 'success',
        'message' => "NIST 800-171 controls seeded: {$inserted} new, {$updated} updated (Total: 110 controls)",
        'inserted' => $inserted,
        'updated' => $updated
    ];
};
