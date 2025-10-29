<?php

/**
 * Enhanced Additional Compliance Frameworks
 *
 * Expands the control coverage for additional frameworks to ~250 total controls.
 * This seed file adds the most commonly assessed and high-impact controls
 * beyond what's in 006_additional_frameworks.php
 *
 * Run this AFTER running 006_additional_frameworks.php
 */

return function($db) {
    $controls = [];

    // =====================================================
    // HIPAA - Additional Controls (~23 more controls)
    // =====================================================
    $controls = array_merge($controls, [
        // Additional Administrative Safeguards
        ['framework' => 'HIPAA', 'code' => '164.308(a)(4)(ii)(A)', 'title' => 'Isolating Health Care Clearinghouse Functions (Required)', 'description' => 'If a health care clearinghouse is part of a larger organization, implement policies and procedures that protect ePHI from the larger organization.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(4)(ii)(B)', 'title' => 'Access Authorization (Addressable)', 'description' => 'Implement policies and procedures for granting access to ePHI, for example through access to a workstation, transaction, program, process, or other mechanism.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(4)(ii)(C)', 'title' => 'Access Establishment and Modification (Addressable)', 'description' => "Implement policies and procedures that, based upon the access authorization, establish, document, review, and modify a user's right of access to a workstation, transaction, program, or process."],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(5)(ii)(A)', 'title' => 'Security Reminders (Addressable)', 'description' => 'Implement periodic security updates and reminders to workforce members.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(5)(ii)(B)', 'title' => 'Protection from Malicious Software (Addressable)', 'description' => 'Implement procedures for guarding against, detecting, and reporting malicious software.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(5)(ii)(C)', 'title' => 'Log-in Monitoring (Addressable)', 'description' => 'Implement procedures for monitoring log-in attempts and reporting discrepancies.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(5)(ii)(D)', 'title' => 'Password Management (Addressable)', 'description' => 'Implement procedures for creating, changing, and safeguarding passwords.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(6)(ii)', 'title' => 'Response and Reporting (Required)', 'description' => 'Identify and respond to suspected or known security incidents; mitigate, to the extent practicable, harmful effects of security incidents; document security incidents and their outcomes.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(7)(ii)(D)', 'title' => 'Testing and Revision Procedures (Addressable)', 'description' => 'Implement procedures for periodic testing and revision of contingency plans.'],
        ['framework' => 'HIPAA', 'code' => '164.308(a)(7)(ii)(E)', 'title' => 'Applications and Data Criticality Analysis (Addressable)', 'description' => 'Assess the relative criticality of specific applications and data in support of other contingency plan components.'],
        ['framework' => 'HIPAA', 'code' => '164.308(b)(3)', 'title' => 'Written Contract or Other Arrangement (Required)', 'description' => 'Document the satisfactory assurances required through a written contract or other arrangement with the business associate.'],

        // Additional Physical Safeguards
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(i)', 'title' => 'Contingency Operations (Addressable)', 'description' => 'Establish and implement procedures that allow facility access in support of restoration of lost data under the disaster recovery plan.'],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(ii)', 'title' => 'Facility Security Plan (Addressable)', 'description' => 'Implement policies and procedures to safeguard the facility and the equipment therein from unauthorized physical access, tampering, and theft.'],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(iii)', 'title' => 'Access Control and Validation Procedures (Addressable)', 'description' => "Implement procedures to control and validate a person's access to facilities based on their role or function."],
        ['framework' => 'HIPAA', 'code' => '164.310(a)(2)(iv)', 'title' => 'Maintenance Records (Addressable)', 'description' => 'Implement policies and procedures to document repairs and modifications to the physical components of a facility.'],
        ['framework' => 'HIPAA', 'code' => '164.310(d)(2)(iii)', 'title' => 'Accountability (Addressable)', 'description' => 'Maintain a record of the movements of hardware and electronic media and any person responsible therefor.'],
        ['framework' => 'HIPAA', 'code' => '164.310(d)(2)(iv)', 'title' => 'Data Backup and Storage (Addressable)', 'description' => 'Create a retrievable, exact copy of ePHI, when needed, before movement of equipment.'],

        // Additional Technical Safeguards
        ['framework' => 'HIPAA', 'code' => '164.312(a)(2)(ii)', 'title' => 'Emergency Access Procedure (Required)', 'description' => 'Establish and implement procedures for obtaining necessary ePHI during an emergency.'],
        ['framework' => 'HIPAA', 'code' => '164.312(c)(2)', 'title' => 'Mechanism to Authenticate ePHI (Addressable)', 'description' => 'Implement electronic mechanisms to corroborate that ePHI has not been altered or destroyed in an unauthorized manner.'],
        ['framework' => 'HIPAA', 'code' => '164.312(e)(2)(i)', 'title' => 'Integrity Controls (Addressable)', 'description' => 'Implement security measures to ensure that electronically transmitted ePHI is not improperly modified without detection until disposed of.'],

        // Breach Notification Rule (related to HIPAA Security)
        ['framework' => 'HIPAA', 'code' => '164.400', 'title' => 'Breach Notification Applicability', 'description' => 'Covered entities and business associates must provide notification of breaches of unsecured protected health information.'],
        ['framework' => 'HIPAA', 'code' => '164.404', 'title' => 'Notification to Individuals', 'description' => 'A covered entity shall notify each individual whose unsecured PHI has been, or is reasonably believed to have been, accessed, acquired, used, or disclosed as a result of a breach.'],
        ['framework' => 'HIPAA', 'code' => '164.406', 'title' => 'Notification to the Media', 'description' => 'For breaches involving more than 500 residents of a State or jurisdiction, provide notice to prominent media outlets.'],
    ]);

    // =====================================================
    // PCI-DSS v4.0 - Enhanced Coverage (~35 more controls)
    // =====================================================
    $controls = array_merge($controls, [
        // Requirement 1: Install and Maintain Network Security Controls (additional)
        ['framework' => 'PCI-DSS', 'code' => '1.1.2', 'title' => 'Roles and Responsibilities for NSC Management', 'description' => 'Roles and responsibilities for performing activities in Requirement 1 are documented, assigned, and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '1.2.2', 'title' => 'NSC Rulesets Restrict Inbound Traffic', 'description' => 'Network security control (NSC) rulesets restrict inbound traffic to the cardholder data environment (CDE).'],
        ['framework' => 'PCI-DSS', 'code' => '1.2.3', 'title' => 'NSC Rulesets Restrict Outbound Traffic', 'description' => 'NSC rulesets restrict outbound traffic from the CDE.'],
        ['framework' => 'PCI-DSS', 'code' => '1.3.1', 'title' => 'Inbound Traffic to CDE is Restricted', 'description' => 'Inbound traffic to the CDE is restricted as follows: to only traffic necessary for the CDE function.'],
        ['framework' => 'PCI-DSS', 'code' => '1.4.1', 'title' => 'NSCs Implemented Between Wireless Networks and CDE', 'description' => 'Network security controls (NSCs) are implemented between wireless networks and the CDE.'],

        // Requirement 2: Apply Secure Configurations (additional)
        ['framework' => 'PCI-DSS', 'code' => '2.1.2', 'title' => 'Wireless Vendor Defaults Changed', 'description' => 'Wireless environments connected to the CDE or transmitting account data are configured with unique encryption keys and default settings changed.'],
        ['framework' => 'PCI-DSS', 'code' => '2.2.1', 'title' => 'Configuration Standards for All System Components', 'description' => 'Configuration standards are developed, implemented, and maintained for all system components.'],
        ['framework' => 'PCI-DSS', 'code' => '2.2.2', 'title' => 'Vendor Defaults Changed', 'description' => 'Vendor defaults are changed before system deployment into a production environment.'],
        ['framework' => 'PCI-DSS', 'code' => '2.2.3', 'title' => 'Primary Functions Separated', 'description' => 'Primary functions requiring different security levels are managed as follows: only one primary function exists on a system component.'],
        ['framework' => 'PCI-DSS', 'code' => '2.3.1', 'title' => 'Wireless Access Points Inventoried', 'description' => 'All wireless access points are identified and monitored, and unauthorized wireless access points are addressed.'],

        // Requirement 3: Protect Stored Account Data (additional)
        ['framework' => 'PCI-DSS', 'code' => '3.1.1', 'title' => 'Data Retention Policy Implemented', 'description' => 'A data retention and disposal policy is implemented that includes cardholder data (CHD) storage amount and retention time.'],
        ['framework' => 'PCI-DSS', 'code' => '3.4.1', 'title' => 'PAN Masked When Displayed', 'description' => 'PAN is masked when displayed (the BIN and last four digits are the maximum number of digits to be displayed).'],
        ['framework' => 'PCI-DSS', 'code' => '3.6.1', 'title' => 'Cryptographic Keys Managed', 'description' => 'Procedures are defined and implemented to protect cryptographic keys used to protect stored account data.'],

        // Requirement 4: Protect Cardholder Data with Strong Cryptography (additional)
        ['framework' => 'PCI-DSS', 'code' => '4.1.1', 'title' => 'Processes for Strong Cryptography', 'description' => 'Processes and mechanisms for protecting cardholder data with strong cryptography during transmission are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '4.2.2', 'title' => 'PAN Not Sent via End-User Messaging', 'description' => 'PAN is secured with strong cryptography whenever it is sent via end-user messaging technologies.'],

        // Requirement 5: Protect Systems and Networks from Malicious Software (additional)
        ['framework' => 'PCI-DSS', 'code' => '5.1.1', 'title' => 'Processes for Malware Protection', 'description' => 'Processes and mechanisms for protecting all systems and networks from malicious software are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '5.2.2', 'title' => 'Anti-Malware Mechanisms Updated', 'description' => 'Deployed anti-malware mechanisms are kept current.'],
        ['framework' => 'PCI-DSS', 'code' => '5.3.1', 'title' => 'Anti-Malware Mechanisms Perform Periodic Scans', 'description' => 'Anti-malware mechanisms perform periodic scans and active scans.'],

        // Requirement 6: Develop and Maintain Secure Systems and Software (additional)
        ['framework' => 'PCI-DSS', 'code' => '6.1.1', 'title' => 'Processes for Secure Systems', 'description' => 'Processes and mechanisms for developing and maintaining secure systems and software are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '6.2.1', 'title' => 'Bespoke Software Accounts for Industry Best Practices', 'description' => 'Bespoke and custom software are developed securely based on industry standards and best practices.'],
        ['framework' => 'PCI-DSS', 'code' => '6.2.2', 'title' => 'Software Development Personnel Trained', 'description' => 'Software development personnel working on bespoke and custom software are trained in secure coding techniques.'],
        ['framework' => 'PCI-DSS', 'code' => '6.3.1', 'title' => 'Security Vulnerabilities Identified', 'description' => 'Security vulnerabilities are identified and addressed.'],
        ['framework' => 'PCI-DSS', 'code' => '6.4.1', 'title' => 'Public-Facing Web Applications Protected', 'description' => 'For public-facing web applications, vulnerabilities are addressed by either automated technical solution(s) or manual assessment.'],

        // Requirement 7: Restrict Access to System Components and Data (additional)
        ['framework' => 'PCI-DSS', 'code' => '7.1.1', 'title' => 'Processes for Access Control', 'description' => 'Processes and mechanisms for restricting access to system components and cardholder data are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '7.2.2', 'title' => 'Access Assigned Based on Job Classification', 'description' => 'Access to system components and data is assigned to users based on their job classification and function.'],
        ['framework' => 'PCI-DSS', 'code' => '7.3.1', 'title' => 'Access Reviewed and Reconfirmed', 'description' => 'User access to system components and data is reviewed at least once every six months.'],

        // Requirement 8: Identify Users and Authenticate Access (additional)
        ['framework' => 'PCI-DSS', 'code' => '8.1.1', 'title' => 'Processes for User Identification', 'description' => 'Processes and mechanisms for identifying and authenticating users are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '8.2.2', 'title' => 'Strong Authentication Factors Implemented', 'description' => 'Strong authentication for users is accomplished by implementing one or more of the defined authentication factors.'],
        ['framework' => 'PCI-DSS', 'code' => '8.3.2', 'title' => 'MFA for Remote Access', 'description' => "MFA is implemented for all remote network access originating from outside the entity's network."],
        ['framework' => 'PCI-DSS', 'code' => '8.4.1', 'title' => 'MFA Systems Protected', 'description' => 'MFA systems are implemented to resist replay attacks.'],
        ['framework' => 'PCI-DSS', 'code' => '8.5.1', 'title' => 'MFA for Administrator Access', 'description' => 'MFA is implemented for all access into the CDE with administrative privileges.'],

        // Requirement 9: Restrict Physical Access (additional)
        ['framework' => 'PCI-DSS', 'code' => '9.1.1', 'title' => 'Physical Access Controls Implemented', 'description' => 'Processes and mechanisms for restricting physical access to cardholder data are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '9.2.1', 'title' => 'Physical Access Controls Limit Access', 'description' => 'Physical access controls limit access to systems that store, process, or transmit account data.'],

        // Requirement 10: Log and Monitor All Access (additional)
        ['framework' => 'PCI-DSS', 'code' => '10.1.1', 'title' => 'Processes for Logging and Monitoring', 'description' => 'Processes and mechanisms for logging and monitoring all access to system components and cardholder data are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '10.2.2', 'title' => 'Audit Logs Capture All User Actions', 'description' => 'Audit logs record all individual user accesses to cardholder data.'],
        ['framework' => 'PCI-DSS', 'code' => '10.3.1', 'title' => 'Audit Log Entries Include Required Elements', 'description' => 'Audit log entries contain details of events and system actions.'],

        // Requirement 11: Test Security (additional)
        ['framework' => 'PCI-DSS', 'code' => '11.1.1', 'title' => 'Processes for Testing Security', 'description' => 'Processes and mechanisms for regularly testing security of systems and networks are defined and understood.'],
        ['framework' => 'PCI-DSS', 'code' => '11.3.2', 'title' => 'Vulnerability Scans Performed', 'description' => 'Internal vulnerability scans are performed as follows: at least once every three months.'],

        // Requirement 12: Support Information Security (additional)
        ['framework' => 'PCI-DSS', 'code' => '12.1.2', 'title' => 'Information Security Roles Assigned', 'description' => 'Responsibility for information security is formally assigned.'],
        ['framework' => 'PCI-DSS', 'code' => '12.3.1', 'title' => 'Security Policies Reviewed Annually', 'description' => 'Security policies and operational procedures are reviewed at least once every 12 months.'],
    ]);

    // =====================================================
    // SOC 2 - Enhanced Coverage (~25 more controls)
    // =====================================================
    $controls = array_merge($controls, [
        // Additional Common Criteria
        ['framework' => 'SOC2', 'code' => 'CC2.2', 'title' => 'Internal Communication', 'description' => 'The entity communicates information internally, including objectives and responsibilities for internal control.'],
        ['framework' => 'SOC2', 'code' => 'CC2.3', 'title' => 'External Communication', 'description' => 'The entity communicates with external parties regarding matters affecting the functioning of internal control.'],
        ['framework' => 'SOC2', 'code' => 'CC4.1', 'title' => 'COSO Monitoring Activities', 'description' => 'The entity selects, develops, and performs ongoing and/or separate evaluations to ascertain whether components are present and functioning.'],
        ['framework' => 'SOC2', 'code' => 'CC4.2', 'title' => 'Evaluation and Communication of Deficiencies', 'description' => 'The entity evaluates and communicates internal control deficiencies in a timely manner to responsible parties.'],
        ['framework' => 'SOC2', 'code' => 'CC5.1', 'title' => 'Control Activities to Achieve Objectives', 'description' => 'The entity selects and develops control activities that contribute to the mitigation of risks.'],
        ['framework' => 'SOC2', 'code' => 'CC5.2', 'title' => 'Technology Control Activities', 'description' => 'The entity selects and develops general control activities over technology.'],
        ['framework' => 'SOC2', 'code' => 'CC5.3', 'title' => 'Deployment of Control Activities', 'description' => 'The entity deploys control activities through policies and procedures.'],
        ['framework' => 'SOC2', 'code' => 'CC6.4', 'title' => 'Restriction to Authorized Programs and Data', 'description' => 'The entity restricts physical and logical access to authorized programs and data.'],
        ['framework' => 'SOC2', 'code' => 'CC6.5', 'title' => 'Access Removed When No Longer Required', 'description' => 'The entity discontinues logical and physical protections when access is no longer required.'],
        ['framework' => 'SOC2', 'code' => 'CC6.8', 'title' => 'Confidential Information Protection', 'description' => 'The entity implements controls to prevent or detect and act upon the introduction of unauthorized or malicious software.'],
        ['framework' => 'SOC2', 'code' => 'CC7.3', 'title' => 'System Monitoring', 'description' => 'The entity evaluates security events to determine whether they could or have resulted in a failure of controls.'],
        ['framework' => 'SOC2', 'code' => 'CC7.4', 'title' => 'Response to Security Incidents', 'description' => 'The entity responds to identified security incidents by executing a defined incident response program.'],
        ['framework' => 'SOC2', 'code' => 'CC7.5', 'title' => 'Identification of Fraudulent Activity', 'description' => 'The entity identifies, analyzes, and responds to risks that affect achievement of its objectives.'],
        ['framework' => 'SOC2', 'code' => 'CC8.2', 'title' => 'System Change Management', 'description' => 'The entity authorizes, tests, and deploys system changes.'],
        ['framework' => 'SOC2', 'code' => 'CC9.2', 'title' => 'Vendor Risk Management', 'description' => 'The entity assesses risks associated with vendors and implements processes to meet its objectives.'],

        // Additional Availability Criteria
        ['framework' => 'SOC2', 'code' => 'A1.1', 'title' => 'Availability Commitments', 'description' => 'The entity maintains, monitors, and evaluates current processing capacity and use.'],
        ['framework' => 'SOC2', 'code' => 'A1.2', 'title' => 'System Availability Monitoring', 'description' => 'The entity authorizes, designs, develops, implements, operates, approves, maintains, and monitors environmental protections.'],
        ['framework' => 'SOC2', 'code' => 'A1.3', 'title' => 'Recovery and Backup', 'description' => 'The entity creates and maintains retrievable exact copies of information to support availability commitments.'],

        // Additional Confidentiality Criteria
        ['framework' => 'SOC2', 'code' => 'C1.1', 'title' => 'Confidentiality Commitments', 'description' => 'The entity identifies and maintains confidential information to meet commitments.'],
        ['framework' => 'SOC2', 'code' => 'C1.2', 'title' => 'Confidential Information Disposal', 'description' => 'The entity disposes of confidential information to meet commitments and system requirements.'],

        // Additional Privacy Criteria
        ['framework' => 'SOC2', 'code' => 'P1.1', 'title' => 'Notice and Communication of Objectives', 'description' => 'The entity provides notice to data subjects about its privacy practices.'],
        ['framework' => 'SOC2', 'code' => 'P2.1', 'title' => 'Choice and Consent', 'description' => 'The entity communicates choices available and obtains implicit or explicit consent.'],
        ['framework' => 'SOC2', 'code' => 'P3.1', 'title' => 'Collection of Personal Information', 'description' => 'Personal information is collected consistent with objectives.'],
        ['framework' => 'SOC2', 'code' => 'P4.1', 'title' => 'Use, Retention, and Disposal', 'description' => 'The entity limits use of personal information to the purposes identified in the notice.'],
        ['framework' => 'SOC2', 'code' => 'P5.1', 'title' => 'Access', 'description' => 'The entity grants data subjects the ability to access their personal information.'],
        ['framework' => 'SOC2', 'code' => 'P6.1', 'title' => 'Disclosure and Notification', 'description' => 'The entity discloses personal information to third parties with consent of data subjects.'],
    ]);

    // =====================================================
    // ISO 27001:2022 - Enhanced Coverage (~32 more controls)
    // =====================================================
    $controls = array_merge($controls, [
        // Additional Organizational Controls (A.5)
        ['framework' => 'ISO27001', 'code' => 'A.5.4', 'title' => 'Management Responsibilities', 'description' => 'Management shall require all personnel to apply information security in accordance with the established policy.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.5', 'title' => 'Contact with Authorities', 'description' => 'The organization shall maintain appropriate contacts with relevant authorities.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.6', 'title' => 'Contact with Special Interest Groups', 'description' => 'The organization shall maintain contact with special interest groups and security forums.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.11', 'title' => 'Return of Assets', 'description' => 'All employees and external party users shall return all organizational assets in their possession upon termination.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.12', 'title' => 'Classification of Information', 'description' => 'Information shall be classified in terms of legal requirements, value, criticality, and sensitivity.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.13', 'title' => 'Labelling of Information', 'description' => 'An appropriate set of procedures shall be developed and implemented for information labelling.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.14', 'title' => 'Information Transfer', 'description' => 'Rules, procedures, or agreements shall cover information transfer.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.18', 'title' => 'Access Rights', 'description' => 'Access rights to information and other associated assets shall be provisioned, reviewed, modified and removed.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.19', 'title' => 'Information Security in Supplier Relationships', 'description' => 'Processes and procedures shall be defined to manage information security risks associated with suppliers.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.20', 'title' => 'Addressing Information Security within Supplier Agreements', 'description' => 'Relevant information security requirements shall be established and agreed with each supplier.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.21', 'title' => 'Managing Information Security in ICT Supply Chain', 'description' => 'Processes and procedures shall be defined to manage information security risks within the ICT supply chain.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.22', 'title' => 'Monitoring, Review and Change Management of Supplier Services', 'description' => 'The organization shall monitor, review, evaluate and manage changes in supplier information security practices.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.24', 'title' => 'Information Security Incident Management Planning', 'description' => 'The organization shall plan and prepare for managing information security incidents.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.25', 'title' => 'Assessment and Decision on Information Security Events', 'description' => 'Information security events shall be assessed to determine if they are to be categorized as incidents.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.26', 'title' => 'Response to Information Security Incidents', 'description' => 'Information security incidents shall be responded to in accordance with documented procedures.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.27', 'title' => 'Learning from Information Security Incidents', 'description' => 'Knowledge gained from information security incidents shall be used to strengthen security.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.28', 'title' => 'Collection of Evidence', 'description' => 'Procedures for identification, collection, acquisition and preservation of evidence shall be defined.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.29', 'title' => 'Information Security During Disruption', 'description' => 'The organization shall plan for information security continuity during adverse situations.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.30', 'title' => 'ICT Readiness for Business Continuity', 'description' => 'ICT readiness shall be planned, implemented, maintained and tested for business continuity.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.31', 'title' => 'Legal, Statutory, Regulatory and Contractual Requirements', 'description' => 'Legal, statutory, regulatory and contractual requirements shall be identified and documented.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.32', 'title' => 'Intellectual Property Rights', 'description' => 'The organization shall implement appropriate procedures to protect intellectual property rights.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.33', 'title' => 'Protection of Records', 'description' => 'Records shall be protected from loss, destruction, falsification and unauthorized access.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.34', 'title' => 'Privacy and Protection of PII', 'description' => 'The organization shall identify and meet requirements regarding preservation of privacy and protection of PII.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.35', 'title' => 'Independent Review of Information Security', 'description' => 'Information security shall be reviewed at planned intervals or when significant changes occur.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.36', 'title' => 'Compliance with Policies, Rules and Standards', 'description' => 'Compliance with organization policies, rules and standards shall be regularly reviewed.'],
        ['framework' => 'ISO27001', 'code' => 'A.5.37', 'title' => 'Documented Operating Procedures', 'description' => 'Operating procedures for information processing facilities shall be documented.'],

        // Additional People Controls (A.6)
        ['framework' => 'ISO27001', 'code' => 'A.6.1', 'title' => 'Screening', 'description' => 'Background verification checks on all candidates for employment shall be carried out.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.2', 'title' => 'Terms and Conditions of Employment', 'description' => 'Employment contractual agreements shall state personnel and organizational responsibilities for information security.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.3', 'title' => 'Information Security Awareness, Education and Training', 'description' => 'Personnel shall receive appropriate information security awareness, education and training.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.4', 'title' => 'Disciplinary Process', 'description' => 'A disciplinary process shall be formalized and communicated to take action against personnel who commit security breaches.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.5', 'title' => 'Responsibilities After Termination or Change', 'description' => 'Information security responsibilities that remain valid after termination or change of employment shall be defined and enforced.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.6', 'title' => 'Confidentiality or Non-disclosure Agreements', 'description' => 'Confidentiality or non-disclosure agreements reflecting organization needs shall be identified and regularly reviewed.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.7', 'title' => 'Remote Working', 'description' => 'Security measures shall be implemented when personnel are working remotely.'],
        ['framework' => 'ISO27001', 'code' => 'A.6.8', 'title' => 'Information Security Event Reporting', 'description' => 'The organization shall provide a mechanism for personnel to report observed or suspected security events.'],
    ]);

    // Insert controls
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

    return $inserted;
};
