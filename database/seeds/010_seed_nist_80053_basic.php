<?php

/**
 * NIST SP 800-53 Rev 5 Controls - Starter Set
 *
 * This seed file contains a representative subset of NIST 800-53 Rev 5 controls
 * across all 20 control families. NIST 800-53 Rev 5 contains over 1,000 controls
 * and control enhancements, which is too large for a single seed file.
 *
 * This starter set includes:
 * - The base control from each of the 20 families
 * - Key controls commonly used in FedRAMP and other federal programs
 *
 * For a complete import of NIST 800-53 Rev 5, you can:
 * 1. Download the official OSCAL (Open Security Controls Assessment Language) JSON/XML from NIST
 * 2. Use the NIST import tool (coming soon) in the Admin panel
 * 3. Manually add additional controls as needed through the admin interface
 *
 * Source: NIST Special Publication 800-53 Revision 5
 * Security and Privacy Controls for Information Systems and Organizations
 */

return function($db) {
    $controls = [
        // AC - Access Control Family
        ['framework' => 'NIST80053', 'code' => 'AC-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate access control policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'AC-2', 'title' => 'Account Management', 'description' => 'Manage system accounts, including establishing, activating, modifying, reviewing, disabling, and removing accounts.'],
        ['framework' => 'NIST80053', 'code' => 'AC-3', 'title' => 'Access Enforcement', 'description' => 'Enforce approved authorizations for logical access to information and system resources.'],
        ['framework' => 'NIST80053', 'code' => 'AC-4', 'title' => 'Information Flow Enforcement', 'description' => 'Enforce approved authorizations for controlling the flow of information within the system and between connected systems.'],
        ['framework' => 'NIST80053', 'code' => 'AC-5', 'title' => 'Separation of Duties', 'description' => 'Separate duties of individuals to prevent malevolent activity without collusion.'],
        ['framework' => 'NIST80053', 'code' => 'AC-6', 'title' => 'Least Privilege', 'description' => 'Employ the principle of least privilege, allowing only authorized accesses for users.'],
        ['framework' => 'NIST80053', 'code' => 'AC-7', 'title' => 'Unsuccessful Logon Attempts', 'description' => 'Enforce a limit of consecutive invalid logon attempts by a user.'],

        // AT - Awareness and Training Family
        ['framework' => 'NIST80053', 'code' => 'AT-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate awareness and training policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'AT-2', 'title' => 'Literacy Training and Awareness', 'description' => 'Provide security and privacy literacy training to system users.'],
        ['framework' => 'NIST80053', 'code' => 'AT-3', 'title' => 'Role-Based Training', 'description' => 'Provide role-based security and privacy training to personnel with assigned security roles and responsibilities.'],

        // AU - Audit and Accountability Family
        ['framework' => 'NIST80053', 'code' => 'AU-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate audit and accountability policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'AU-2', 'title' => 'Event Logging', 'description' => 'Identify the types of events that the system is capable of logging.'],
        ['framework' => 'NIST80053', 'code' => 'AU-3', 'title' => 'Content of Audit Records', 'description' => 'Ensure that audit records contain information that establishes the outcome of events.'],
        ['framework' => 'NIST80053', 'code' => 'AU-6', 'title' => 'Audit Record Review, Analysis, and Reporting', 'description' => 'Review and analyze system audit records for indications of inappropriate or unusual activity.'],

        // CA - Assessment, Authorization, and Monitoring Family
        ['framework' => 'NIST80053', 'code' => 'CA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate assessment, authorization, and monitoring policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'CA-2', 'title' => 'Control Assessments', 'description' => 'Select the appropriate assessor or assessment team for the type of assessment to be conducted.'],
        ['framework' => 'NIST80053', 'code' => 'CA-3', 'title' => 'Information Exchange', 'description' => 'Approve and manage the exchange of information between the system and other systems.'],
        ['framework' => 'NIST80053', 'code' => 'CA-5', 'title' => 'Plan of Action and Milestones', 'description' => 'Develop a plan of action and milestones for the system to document the planned remediation actions.'],
        ['framework' => 'NIST80053', 'code' => 'CA-7', 'title' => 'Continuous Monitoring', 'description' => 'Develop a system-level continuous monitoring strategy and implement continuous monitoring.'],

        // CM - Configuration Management Family
        ['framework' => 'NIST80053', 'code' => 'CM-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate configuration management policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'CM-2', 'title' => 'Baseline Configuration', 'description' => 'Develop, document, and maintain a current baseline configuration of the system.'],
        ['framework' => 'NIST80053', 'code' => 'CM-3', 'title' => 'Configuration Change Control', 'description' => 'Determine and document the types of changes to the system that are configuration-controlled.'],
        ['framework' => 'NIST80053', 'code' => 'CM-4', 'title' => 'Impact Analyses', 'description' => 'Analyze changes to the system to determine potential security and privacy impacts prior to change implementation.'],
        ['framework' => 'NIST80053', 'code' => 'CM-6', 'title' => 'Configuration Settings', 'description' => 'Establish and document configuration settings for components employed within the system.'],
        ['framework' => 'NIST80053', 'code' => 'CM-7', 'title' => 'Least Functionality', 'description' => 'Configure the system to provide only mission-essential capabilities.'],

        // CP - Contingency Planning Family
        ['framework' => 'NIST80053', 'code' => 'CP-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate contingency planning policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'CP-2', 'title' => 'Contingency Plan', 'description' => 'Develop a contingency plan for the system that is consistent with contingency requirements.'],
        ['framework' => 'NIST80053', 'code' => 'CP-3', 'title' => 'Contingency Training', 'description' => 'Provide contingency training to system users consistent with assigned roles and responsibilities.'],
        ['framework' => 'NIST80053', 'code' => 'CP-4', 'title' => 'Contingency Plan Testing', 'description' => 'Test the contingency plan for the system to determine the effectiveness of the plan.'],
        ['framework' => 'NIST80053', 'code' => 'CP-9', 'title' => 'System Backup', 'description' => 'Conduct backups of user-level information, system-level information, and system documentation.'],
        ['framework' => 'NIST80053', 'code' => 'CP-10', 'title' => 'System Recovery and Reconstitution', 'description' => 'Provide for the recovery and reconstitution of the system to a known state.'],

        // IA - Identification and Authentication Family
        ['framework' => 'NIST80053', 'code' => 'IA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate identification and authentication policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'IA-2', 'title' => 'Identification and Authentication (Organizational Users)', 'description' => 'Uniquely identify and authenticate organizational users and associate that identity with processes.'],
        ['framework' => 'NIST80053', 'code' => 'IA-3', 'title' => 'Device Identification and Authentication', 'description' => 'Uniquely identify and authenticate devices before establishing a connection.'],
        ['framework' => 'NIST80053', 'code' => 'IA-4', 'title' => 'Identifier Management', 'description' => 'Manage system identifiers by receiving authorization to assign identifiers.'],
        ['framework' => 'NIST80053', 'code' => 'IA-5', 'title' => 'Authenticator Management', 'description' => 'Manage system authenticators by verifying the identity of the individual, group, role, or device.'],
        ['framework' => 'NIST80053', 'code' => 'IA-8', 'title' => 'Identification and Authentication (Non-Organizational Users)', 'description' => 'Uniquely identify and authenticate non-organizational users or processes acting on behalf of non-organizational users.'],

        // IR - Incident Response Family
        ['framework' => 'NIST80053', 'code' => 'IR-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate incident response policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'IR-2', 'title' => 'Incident Response Training', 'description' => 'Provide incident response training to system users consistent with assigned roles and responsibilities.'],
        ['framework' => 'NIST80053', 'code' => 'IR-4', 'title' => 'Incident Handling', 'description' => 'Implement an incident handling capability for incidents.'],
        ['framework' => 'NIST80053', 'code' => 'IR-5', 'title' => 'Incident Monitoring', 'description' => 'Track and document incidents.'],
        ['framework' => 'NIST80053', 'code' => 'IR-6', 'title' => 'Incident Reporting', 'description' => 'Require personnel to report suspected incidents to the organizational incident response capability.'],
        ['framework' => 'NIST80053', 'code' => 'IR-8', 'title' => 'Incident Response Plan', 'description' => 'Develop an incident response plan that provides a roadmap for implementing an incident response capability.'],

        // MA - Maintenance Family
        ['framework' => 'NIST80053', 'code' => 'MA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate maintenance policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'MA-2', 'title' => 'Controlled Maintenance', 'description' => 'Schedule, document, and review records of maintenance, repair, and replacement on system components.'],
        ['framework' => 'NIST80053', 'code' => 'MA-4', 'title' => 'Nonlocal Maintenance', 'description' => 'Approve and monitor nonlocal maintenance and diagnostic activities.'],
        ['framework' => 'NIST80053', 'code' => 'MA-5', 'title' => 'Maintenance Personnel', 'description' => 'Establish a process for maintenance personnel authorization and maintain a list of authorized personnel.'],

        // MP - Media Protection Family
        ['framework' => 'NIST80053', 'code' => 'MP-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate media protection policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'MP-2', 'title' => 'Media Access', 'description' => 'Restrict access to system media to authorized individuals.'],
        ['framework' => 'NIST80053', 'code' => 'MP-3', 'title' => 'Media Marking', 'description' => 'Mark system media indicating the distribution limitations, handling caveats, and applicable security markings.'],
        ['framework' => 'NIST80053', 'code' => 'MP-4', 'title' => 'Media Storage', 'description' => 'Physically control and securely store system media within controlled areas.'],
        ['framework' => 'NIST80053', 'code' => 'MP-6', 'title' => 'Media Sanitization', 'description' => 'Sanitize system media prior to disposal, release out of organizational control, or release for reuse.'],

        // PE - Physical and Environmental Protection Family
        ['framework' => 'NIST80053', 'code' => 'PE-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate physical and environmental protection policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'PE-2', 'title' => 'Physical Access Authorizations', 'description' => 'Develop, approve, and maintain a list of individuals with authorized access to the facility.'],
        ['framework' => 'NIST80053', 'code' => 'PE-3', 'title' => 'Physical Access Control', 'description' => 'Enforce physical access authorizations at entry and exit points to the facility.'],
        ['framework' => 'NIST80053', 'code' => 'PE-6', 'title' => 'Monitoring Physical Access', 'description' => 'Monitor physical access to the facility where the system resides to detect and respond to physical security incidents.'],

        // PL - Planning Family
        ['framework' => 'NIST80053', 'code' => 'PL-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate planning policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'PL-2', 'title' => 'System Security and Privacy Plans', 'description' => 'Develop security and privacy plans for the system that are consistent with the organization\'s architecture.'],
        ['framework' => 'NIST80053', 'code' => 'PL-4', 'title' => 'Rules of Behavior', 'description' => 'Establish and provide to individuals requiring access to the system, the rules that describe their responsibilities and expected behavior.'],

        // PM - Program Management Family
        ['framework' => 'NIST80053', 'code' => 'PM-1', 'title' => 'Information Security Program Plan', 'description' => 'Develop and disseminate an organization-wide information security program plan.'],
        ['framework' => 'NIST80053', 'code' => 'PM-2', 'title' => 'Information Security Program Leadership Role', 'description' => 'Appoint a senior agency information security officer with the mission and resources to coordinate information security program.'],

        // PS - Personnel Security Family
        ['framework' => 'NIST80053', 'code' => 'PS-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate personnel security policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'PS-2', 'title' => 'Position Risk Designation', 'description' => 'Assign a risk designation to all organizational positions.'],
        ['framework' => 'NIST80053', 'code' => 'PS-3', 'title' => 'Personnel Screening', 'description' => 'Screen individuals prior to authorizing access to the system.'],
        ['framework' => 'NIST80053', 'code' => 'PS-4', 'title' => 'Personnel Termination', 'description' => 'Upon termination of individual employment, disable system access, conduct exit interviews, retrieve organizational property, and retain access to organizational information and systems.'],

        // PT - Personally Identifiable Information Processing and Transparency Family
        ['framework' => 'NIST80053', 'code' => 'PT-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate personally identifiable information processing and transparency policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'PT-2', 'title' => 'Authority to Collect', 'description' => 'Determine and document the legal authority that permits the collection, use, maintenance, and sharing of PII.'],
        ['framework' => 'NIST80053', 'code' => 'PT-3', 'title' => 'Personally Identifiable Information Processing Purposes', 'description' => 'Identify and document the purpose(s) for processing PII.'],

        // RA - Risk Assessment Family
        ['framework' => 'NIST80053', 'code' => 'RA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate risk assessment policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'RA-2', 'title' => 'Security Categorization', 'description' => 'Categorize the system and information it processes, stores, and transmits.'],
        ['framework' => 'NIST80053', 'code' => 'RA-3', 'title' => 'Risk Assessment', 'description' => 'Conduct a risk assessment, including identifying threats to and vulnerabilities in the system.'],
        ['framework' => 'NIST80053', 'code' => 'RA-5', 'title' => 'Vulnerability Monitoring and Scanning', 'description' => 'Monitor and scan for vulnerabilities in the system and hosted applications.'],

        // SA - System and Services Acquisition Family
        ['framework' => 'NIST80053', 'code' => 'SA-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate system and services acquisition policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SA-2', 'title' => 'Allocation of Resources', 'description' => 'Determine security and privacy requirements for the system in mission and business process planning.'],
        ['framework' => 'NIST80053', 'code' => 'SA-3', 'title' => 'System Development Life Cycle', 'description' => 'Acquire, develop, and manage the system using a system development life cycle methodology.'],
        ['framework' => 'NIST80053', 'code' => 'SA-4', 'title' => 'Acquisition Process', 'description' => 'Include security and privacy requirements, descriptions, and criteria in acquisition contracts.'],

        // SC - System and Communications Protection Family
        ['framework' => 'NIST80053', 'code' => 'SC-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate system and communications protection policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SC-2', 'title' => 'Separation of System and User Functionality', 'description' => 'Separate user functionality, including user interface services, from system management functionality.'],
        ['framework' => 'NIST80053', 'code' => 'SC-5', 'title' => 'Denial-of-Service Protection', 'description' => 'Protect against or limit the effects of denial-of-service attacks.'],
        ['framework' => 'NIST80053', 'code' => 'SC-7', 'title' => 'Boundary Protection', 'description' => 'Monitor and control communications at the external managed interfaces to the system.'],
        ['framework' => 'NIST80053', 'code' => 'SC-8', 'title' => 'Transmission Confidentiality and Integrity', 'description' => 'Protect the confidentiality and integrity of transmitted information.'],
        ['framework' => 'NIST80053', 'code' => 'SC-12', 'title' => 'Cryptographic Key Establishment and Management', 'description' => 'Establish and manage cryptographic keys for cryptography employed in organizational systems.'],
        ['framework' => 'NIST80053', 'code' => 'SC-13', 'title' => 'Cryptographic Protection', 'description' => 'Implement cryptographic mechanisms to prevent unauthorized disclosure and modification of information.'],

        // SI - System and Information Integrity Family
        ['framework' => 'NIST80053', 'code' => 'SI-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate system and information integrity policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SI-2', 'title' => 'Flaw Remediation', 'description' => 'Identify, report, and correct system flaws.'],
        ['framework' => 'NIST80053', 'code' => 'SI-3', 'title' => 'Malicious Code Protection', 'description' => 'Implement malicious code protection mechanisms at system entry and exit points.'],
        ['framework' => 'NIST80053', 'code' => 'SI-4', 'title' => 'System Monitoring', 'description' => 'Monitor the system to detect attacks and indicators of potential attacks, unauthorized connections, and unusual activity.'],
        ['framework' => 'NIST80053', 'code' => 'SI-5', 'title' => 'Security Alerts, Advisories, and Directives', 'description' => 'Receive system security alerts, advisories, and directives from designated external organizations on an ongoing basis.'],

        // SR - Supply Chain Risk Management Family
        ['framework' => 'NIST80053', 'code' => 'SR-1', 'title' => 'Policy and Procedures', 'description' => 'Develop, document, and disseminate supply chain risk management policy and procedures.'],
        ['framework' => 'NIST80053', 'code' => 'SR-2', 'title' => 'Supply Chain Risk Management Plan', 'description' => 'Develop a plan for managing supply chain risks associated with the research and development, design, manufacturing, acquisition, delivery, integration, operations, maintenance, and disposal of systems and components.'],
        ['framework' => 'NIST80053', 'code' => 'SR-3', 'title' => 'Supply Chain Controls and Processes', 'description' => 'Establish a process or processes to identify and address weaknesses or deficiencies in the supply chain.'],
    ];

    $inserted = 0;
    $updated = 0;
    $skipped = 0;

    foreach ($controls as $control) {
        // Check if control already exists
        $existing = $db->fetchOne(
            "SELECT id FROM controls WHERE framework = ? AND code = ?",
            [$control['framework'], $control['code']]
        );

        if (!$existing) {
            $db->insert('controls', $control);
            $inserted++;
        } else {
            $skipped++;
        }
    }

    echo "\nNIST 800-53 Seed Results:\n";
    echo "- Inserted: $inserted controls\n";
    echo "- Skipped (already exist): $skipped controls\n";
    echo "\nNote: This is a starter set. NIST 800-53 Rev 5 contains over 1,000 controls total.\n";
    echo "For complete coverage, consider importing the official OSCAL catalog.\n";
};
